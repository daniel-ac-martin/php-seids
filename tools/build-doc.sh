#! /bin/sh
#===============================================================================
# PHP SEIDS: Supplementary, Easily Interchangeable Data Structures
# 
# Copyright 2015, Daniel A.C. Martin
# Distributed under the MIT License.
# (See LICENSE file for details.)
#===============================================================================

SCRIPT_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
PROJECT_DIR="${SCRIPT_DIR}/.."
SRC_DIR="${PROJECT_DIR}/doc"
BUILD_DIR="${PROJECT_DIR}/build/doc"

EXEC_DIR="${PROJECT_DIR}/vendor/bin"

MERGED="${BUILD_DIR}/.manual.xml"

"${EXEC_DIR}/apidoc"
if [ "${?}" == "0" ]; then
	cp -a "${SRC_DIR}/entities" "${BUILD_DIR}"
	echo "Merging API documentation with main documentation..."
	xmllint --xinclude --noent -o "${MERGED}" "${SRC_DIR}/manual.xml"
	echo "Creating versions file..."
	xmllint --xinclude --noent -o "${BUILD_DIR}/version.xml" "${SRC_DIR}/version.xml"
	echo "Making dynamic website..."
	phd --docbook "${MERGED}" --package PHP --format php    --output "${BUILD_DIR}/dynamic-website"
	#echo "Making static website..."
	#phd --docbook "${MERGED}" --package PHP --format xhtml  --output "${BUILD_DIR}/static-website"
	echo "Making PDF..."
	phd --docbook "${MERGED}" --package PHP --format bigpdf --output "${BUILD_DIR}/pdf"
fi

