include local_config.mk

GPSEE_PREFIX 		?= $(shell $(GPSEE_CONFIG) --gpsee-prefix)
GPSEE_EXEC_PREFIX       ?= $(shell $(GPSEE_CONFIG) --exec-prefix)
PRECOMPILER             ?= $(GPSEE_EXEC_PREFIX)/gpsee_precompiler
GSR			?= /usr/bin/gsr

PROGRAM_DIR		?= /usr/bin
PROGS			?= vr09-regedit

top:	install

install: $(GPSEE_PREFIX)/libexec $(PROGRAM_DIR)
	cp -pf $(wildcard vr09/*.js) $(GPSEE_PREFIX)/libexec
	cp -pf $(PROGS) $(PROGRAM_DIR)
	$(shell $(foreach PROG, $(PROGS), sed 's;^#! /usr/bin/gsr;$(GSR);' < $(PROG) > $(PROGRAM_DIR)/$(PROGS); ))
	$(shell $(foreach PROG, $(PROGS), $(PRECOMPILER) $(PROGRAM_DIR)/$(PROGS); ))

$(GPSEE_PREFIX)/libexec $(PROGRAM_DIR):
	mkdir -p $@

