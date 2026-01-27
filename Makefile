SHELL := /bin/bash
APPNAME := onboard

REQS := sassc msgfmt
K := $(foreach r, ${REQS}, $(if $(shell command -v ${r} 2> /dev/null), '', $(error "${r} not installed")))

LANGUAGES := $(wildcard language/*/LC_MESSAGES)
JAVASCRIPT := $(shell find public -name '*.js' ! -name '*-*.js')

VERSION := $(shell cat VERSION | tr -d "[:space:]")
COMMIT := $(shell git rev-parse --short HEAD)

default: test clean compile package

clean:
	rm -Rf build/${APPNAME}*
	rm -f public/js/*-*.js

compile:
	for f in ${JAVASCRIPT}; do cp $$f $${f%.js}-${VERSION}.js; done
	for f in $(LANGUAGES); do \
		msgfmt -cv $$f/errors.po -o $$f/errors.mo; \
		msgfmt -cv $$f/labels.po -o $$f/labels.mo; \
		msgfmt -cv $$f/messages.po -o $$f/messages.mo; \
	done

package:
	[[ -d build ]] || mkdir build
	rsync -rl --exclude-from=buildignore . build/${APPNAME}
	cd build && tar czf ${APPNAME}-${VERSION}-${COMMIT}.tar.gz ${APPNAME}

test:
	vendor/bin/phpunit -c src/Test/phpunit.xml --testsuite Unit
	vendor/bin/phpstan analyse -l 0
