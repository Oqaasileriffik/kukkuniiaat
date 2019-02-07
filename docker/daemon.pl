#!/usr/bin/env perl
# -*- mode: cperl; indent-tabs-mode: nil; tab-width: 3; cperl-indent-level: 3; -*-
use utf8;
BEGIN { $| = 1; }
package Daemon;

use warnings;
use strict;
use IPC::Open2;
use IO::Select;
use Getopt::Long;
use base qw(Net::Server::PreFork);

my $cmd = 'cat';
my $port = 20203;
my $rop = GetOptions(
   "port|p=s" => \$port,
   "cmd|c=s" => \$cmd
   );

$ENV{'GT_DAEMON'} = 1;

my $daemon = Daemon->new({
   port => $port,
   host => '*',
   ipv => 4,
   min_servers => 2,
   max_servers => 40,
   min_spare_servers => 2,
   max_spare_servers => 5,
   max_requests => 50,
});
$daemon->{cmd} = $cmd;
$daemon->run;

sub post_bind_hook {
   if ($port =~  m~^([^|]+)\|unix$~) {
      print STDERR "Unix socket detected, making $1 world-writable\n";
      chmod 0777, $1;
   }
}

sub child_init_hook {
   my $self = shift;
   $self->{pid} = open2(\*OUT, \*IN, $self->{cmd});
}

sub process_request {
   my $self = shift;

   my $sl = IO::Select->new();
   $sl->add(\*OUT);

   my $length = 0;
   print STDERR "\nPID ".$$.": Request in progress...\n";
   eval {
      local $SIG{'ALRM'} = sub { die "Timed Out!\n" };
      my $timeout = 90;

      my $previous_alarm = alarm($timeout);
      my $input = '';
      while (<STDIN>) {
         if (/^<END-OF-INPUT>$/) {
            last;
         }
         $input .= $_;
         $length += length $_;
         #print STDERR "Seen: ", $_, "\n";
         if (length $input >= 4096) {
            print IN $input;
            my @cr = $sl->can_read(0.1);
            foreach my $r (@cr) {
               my $l = $r->getline();
               print STDOUT $l;
            }
            alarm($timeout);
            $input = '';
         }
      }
      print IN $input;
      #print STDERR "Closing...\n";
      close IN;
      #print STDERR "Flushing...\n";
      my $output = '';
      while (<OUT>) {
         $output .= $_;
         if (length $output >= 4096) {
            print STDOUT $output;
            $output = '';
         }
      }
      print STDOUT $output;
      close OUT;
      #print STDERR "Reaping...\n";
      waitpid($self->{pid}, 0);
      #print STDERR "Renewing...\n";
      $self->{pid} = open2(\*OUT, \*IN, $self->{cmd});
      alarm($previous_alarm);
   };

   if ($@ =~ /timed out/i) {
      print STDERR "\nPID ".$$.": Request timed out ($length bytes processed).\n";
      print STDOUT "\n<TIMED-OUT>\n";
      return;
   }
   print STDERR "\nPID ".$$.": Request completed ($length bytes processed).\n";
}

1;
