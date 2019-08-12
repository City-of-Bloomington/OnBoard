SHELL := /bin/bash
APPNAME := onboard

SASS := $(shell command -v sassc 2> /dev/null)
MSGFMT := $(shell command -v msgfmt 2> /dev/null)

LANGUAGES := $(wildcard language/*/LC_MESSAGES)

default: clean compile package

deps:
ifndef SASS
	$(error "sassc is not installed")
endif
ifndef MSGFMT
	$(error "msgfmt is not installed, please install gettext")
endif

clean:
	rm -Rf build/${APPNAME}*

	rm -Rf public/css/.sass-cache
	rm -Rf data/Themes/Kirkwood/public/css/.sass-cache

compile: deps $(LANGUAGES)
	cd public/css                      && sassc -t compact -m screen.scss screen.css
	cd data/Themes/Kirkwood/public/css && sassc -t compact -m screen.scss screen.css

package:
	[[ -d build ]] || mkdir build
	rsync -rl --exclude-from=buildignore . build/${APPNAME}
	cd build && tar czf ${APPNAME}-${VERSION}.tar.gz ${APPNAME}

$(LANGUAGES): deps
	cd $@ && msgfmt -cv *.po
