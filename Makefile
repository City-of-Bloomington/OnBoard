SHELL := /bin/bash
APPNAME := onboard

REQS := sassc msgfmt
K := $(foreach r, ${REQS}, $(if $(shell command -v ${r} 2> /dev/null), '', $(error "${r} not installed")))

LANGUAGES := $(wildcard language/*/LC_MESSAGES)
JAVASCRIPT := $(shell find public -name '*.js' ! -name '*-*.js')

VERSION := $(shell cat VERSION | tr -d "[:space:]")

default: clean compile package

clean:
	rm -Rf build/${APPNAME}*

	for f in $(shell find public/js   -name '*-*.js'       ); do rm $$f; done

compile: $(CSS)
	for f in ${JAVASCRIPT}; do cp $$f $${f%.js}-${VERSION}.js; done
	cd ${LANGUAGES} && msgfmt -cv *.po

package:
	[[ -d build ]] || mkdir build
	rsync -rl --exclude-from=buildignore . build/${APPNAME}
	cd build && tar czf ${APPNAME}-${VERSION}.tar.gz ${APPNAME}

test:
	vendor/phpunit/phpunit/phpunit -c src/Test/Unit.xml

$(LANGUAGES):
	cd $@ && msgfmt -cv *.po
