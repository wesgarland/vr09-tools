#! /bin/sh
#
# Script to install vr09-tools when a proper build system (e.g. GNU make) is not
# available.  This should not be used unless you really know what you are doing,
# and are willing to troubleshoot it if necessary. It is not maintained and only 
# tested once, on Mac OS X 10.7.
#

GPSEE_CONFIG=`readlink /usr/bin/gsr | sed 's;/gsr$;/gpsee-config;'`
GPSEE_PREFIX=`${GPSEE_CONFIG} --prefix`
GPSEE_EXEC_PREFIX=`${GPSEE_CONFIG} --exec-prefix`
PRECOMPILER=`${GPSEE_EXEC_PREFIX}/gpsee_precompiler`
GSR=/usr/bin/gsr
LIB_DIR=${GPSEE_PREFIX}/libexec/vr09
PROGRAM_DIR=/usr/bin
PROGS=vr09-regedit

echo mkdir -p ${LIB_DIR} ${PROGRAM_DIR}
mkdir -p ${LIB_DIR} ${PROGRAM_DIR}
echo cp -pf vr09/*.js ${LIB_DIR}
cp -pf vr09/*.js ${LIB_DIR}
echo cp -pf ${PROGS} ${PROGRAM_DIR}
cp -pf ${PROGS} ${PROGRAM_DIR}
for PROG in ${PROGS}
do
  echo sed "s;^#! /usr/bin/gsr;#! ${GSR};" < ${PROG} > ${PROGRAM_DIR}/${PROGS};
  sed "s;^#! /usr/bin/gsr;#! ${GSR};" < ${PROG} > ${PROGRAM_DIR}/${PROGS};
done


