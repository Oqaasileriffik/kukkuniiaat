#!/usr/bin/env perl
# -*- mode: cperl; indent-tabs-mode: nil; tab-width: 3; cperl-indent-level: 3; -*-
use strict;
use warnings;
use utf8;

BEGIN {
	$| = 1;
	binmode(STDIN, ':encoding(UTF-8)');
	binmode(STDOUT, ':encoding(UTF-8)');
}
use open qw( :encoding(UTF-8) :std );
use feature 'unicode_strings';

my $wf = '';
my $type = '';
my @suggs = ();
my $noncg = '';

my $print_cohort = sub {
   if (!$wf) {
      return;
   }

   print "$wf";
   if ($type) {
      print "\t$type";
      if (@suggs) {
         for my $sug (@suggs) {
            print " <R:$sug>";
         }
      }
   }
   print "\n";
   print "$noncg";

   $wf = '';
   $type = '';
   @suggs = ();
   $noncg = '';
};

while (<STDIN>) {
   if (m@^<s@ || m@^</s@) {
      $print_cohort->();
      print;
   }
   elsif (m@^"<(.*)>"@) {
      $print_cohort->();
      $wf = $1;
   }
   elsif (m@^\s+"@) {
      if (m@ \?@) {
         $type = '@unknown';
      }
      elsif (m@^\s+".*".+<spelled>@ && m@"<(.*?)>"@) {
         $type = '@spell';
         push(@suggs, $1);
      }
   }
   else {
      if ($wf) {
         $noncg .= $_;
      }
      else {
         print;
      }
   }
}

$print_cohort->();
