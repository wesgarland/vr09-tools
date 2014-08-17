include local_config.mk

GPSEE_PREFIX 		?= $(shell $(GPSEE_CONFIG) --prefix)
GPSEE_EXEC_PREFIX       ?= $(shell $(GPSEE_CONFIG) --exec-prefix)
PRECOMPILER             ?= $(GPSEE_EXEC_PREFIX)/gpsee_precompiler
GSR			?= /usr/bin/gsr

LIB_DIR			?= $(GPSEE_PREFIX)/libexec/vr09
PROGRAM_DIR		?= /usr/bin
PROGS			?= vr09-regedit

top:	install

install: $(LIB_DIR) $(PROGRAM_DIR)
	cp -pf $(wildcard vr09/*.js) $(LIB_DIR)
	cp -pf $(PROGS) $(PROGRAM_DIR)
	$(shell $(foreach PROG, $(PROGS), sed 's;^#! /usr/bin/gsr;#! $(GSR);' < $(PROG) > $(PROGRAM_DIR)/$(PROGS); ))
#	$(shell $(foreach PROG, $(PROGS), $(PRECOMPILER) $(PROGRAM_DIR)/$(PROGS); ))

$(LIB_DIR) $(PROGRAM_DIR):
	mkdir -p $@

