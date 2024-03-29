SHELL := /bin/bash
APPNAME := onboard

REQS := sassc msgfmt
K := $(foreach r, ${REQS}, $(if $(shell command -v ${r} 2> /dev/null), '', $(error "${r} not installed")))

LANGUAGES := $(wildcard language/*/LC_MESSAGES)
JAVASCRIPT := $(shell find public -name '*.js' ! -name '*-*.js')

VERSION := $(shell cat VERSION | tr -d "[:space:]")

default: clean compile test package

clean:
	rm -Rf build/${APPNAME}*

	rm -Rf public/css/.sass-cache
	rm -Rf data/Themes/Kirkwood/public/css/.sass-cache
	for f in $(shell find data/Themes -name 'screen-*.css*'); do rm $$f; done
	for f in $(shell find public/css  -name 'screen-*.css*'); do rm $$f; done
	for f in $(shell find public/js   -name '*-*.js'       ); do rm $$f; done

compile: $(LANGUAGES)
	cd public/css                      && sassc -t compact -m screen.scss screen-${VERSION}.css
	cd data/Themes/Kirkwood/public/css && sassc -t compact -m screen.scss screen-${VERSION}.css
	for f in ${JAVASCRIPT}; do cp $$f $${f%.js}-${VERSION}.js; done

package:
	[[ -d build ]] || mkdir build
	rsync -rl --exclude-from=buildignore . build/${APPNAME}
	cd build && tar czf ${APPNAME}-${VERSION}.tar.gz ${APPNAME}

test:
	vendor/phpunit/phpunit/phpunit -c src/Test/Unit.xml

$(LANGUAGES):
	cd $@ && msgfmt -cv *.po
