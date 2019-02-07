FROM debian:stretch

LABEL vendor="Tino Didriksen" \
	maintainer="Tino Didriksen <mail@tinodidriksen.com>" \
	com.tinodidriksen.product="Kukkuniaat Backend" \
	com.tinodidriksen.codename="kukkuniaat-backend"

ENV LANG=C.UTF-8 \
	LC_ALL=C.UTF-8 \
	DEBIAN_FRONTEND=noninteractive \
	DEBCONF_NONINTERACTIVE_SEEN=true \
	PERL_UNICODE=SDA

RUN apt-get update
RUN apt-get install -fy --no-install-recommends apt-utils
RUN apt-get install -fy --no-install-recommends wget ca-certificates
RUN wget https://apertium.projectjj.com/apt/install-nightly.sh -O - | bash
RUN apt-get install -fy --no-install-recommends \
		giella-kal \
		divvun-gramcheck \
		perl \
		libipc-run-perl \
		liblingua-identify-perl \
		libmldbm-sync-perl \
		libnet-server-perl \
		libstring-approx-perl
RUN apt-get remove -fy wget ca-certificates apt-utils
RUN apt-get autoremove -fy
RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/*
RUN groupadd -r speller && useradd --no-log-init -r -g speller speller

COPY . /backend/
RUN chown -R speller:speller /backend

USER speller

ENV PORT=12400
EXPOSE $PORT
CMD /backend/daemon.pl -c /backend/speller.sh -p $PORT
