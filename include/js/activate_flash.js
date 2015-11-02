
/* $Id: activate_flash.js 62 2009-02-18 02:59:27Z john $ */

// This file will no longer be included by default. A minified version is included as part of core-min.js

theObjects = document.getElementsByTagName("object");
for (var i = 0; i < theObjects.length; i++) {
theObjects[i].outerHTML = theObjects[i].outerHTML;
}
