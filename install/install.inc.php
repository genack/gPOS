<?php

/* Funciones necesarias */


function ErrorMensaje($mensaje,$fatal=false){
	echo "<div style='background-color: #eee; border:2px solid red;padding:8px'>";
	//echo "<h3>".$UltimaTarea. "</h3>";
	if ($mensaje){ 
		echo ($fatal?"Error fatal: ":"Error: ");	
		echo $mensaje;
	} else {
		if ($fatal) {
			echo "Se ha producido un error fatal.";	
		} else 
			echo "Se ha producido un error.";	
	}
	echo "</div>";	
}
	



function webAssert($condicionTrue,$mensajeOk,$mensajeError,$fatal=false){
	global $numErrores,$numFatal,$UltimaTarea;
	if ($condicionTrue) {		
		echo $mensajeOk;
		
		if($mensajeOk and $mensajeOk!="" and $mensajeOk!=".")
			echo "<br>";					
		
		return 0;
	}
	
	//Fallo:	

	if ($fatal) 
		$numFatal++;
	else
		$numErrores++;


	echo "<div style='background-color: #eee; border:2px solid red;padding:8px'>";
	//echo "<h3>".$UltimaTarea. "</h3>";
	if ($mensajeError){ 
		echo ($fatal?"Error fatal: ":"Error: ");	
		echo $mensajeError;
	} else {
		if ($fatal) {
			echo "Se ha producido un error fatal.";	
		} else 
			echo "Se ha producido un error.";	
	}
	echo "</div>";
	
	if ($fatal)
		die;
	
	return 1;//numero de errores		
} 



function IniciaTarea($mensaje){
	//global $UltimaTarea;
	//$UltimaTarea = $mensaje;
	echo "<h2>",$mensaje,"</h2>";	
}

function split_queris($bigcode){
	$lines = explode("\n",$bigcode);
	$out = array();
	$buffer = "";
	
	foreach( $lines as $line ){	
		if (!preg_match('/^;;;;;;/', $line)){
			$buffer .= $line;
		}	else {
			$out[] = $buffer;
			$buffer = "";						
		}		
	}	
	return $out;	
}

function PresentarInterface($interface,$datos=false){	
	include($interface);	
}

function bootstrapDomain($xurl){
	$aurl = parse_url($xurl);
	return $aurl['host'];
}

function bootStrapJS($xdomain){

$jsOutFile = '/**
 * Copyright 2013 Jorge Villalobos
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0

 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

const Cc = Components.classes;
const Ci = Components.interfaces;
const Ur = "'.$xdomain.'";


function install(aData, aReason) {}

function uninstall(aData, aReason) {}

function shutdown(aData, aReason) {}

function startup(aData, aReason) {
  RXULMInstaller.init(aReason);
}

var RXULMInstaller = {

  ALLOW_REMOTE_XUL : "allowXULXBL",
  ALLOW : 1,
  LOCAL_FILES : "<file>",
  LOCAL_FILE_PREF : "dom.allow_XUL_XBL_for_file",

  // The list of domains to include on the whitelist.
  PERMISSIONS : [ Ur ],
  // Title for dialogs and optional, localized version of the message.
  TITLE : "gPOS - Remote XUL Manager",
  TITLE_LOCALIZED : "gPOS - Remote XUL Manager",
  // Installation warning and optional, localized version of the message.
  WARNING :
    "The following list of domains will be added to your remote XUL " +
    "whitelist. Select OK to accept.",
  WARNING_LOCALIZED : "gPOS:  Agregar el dominio en la lista blanca de XUL Remoto.",
  SILENT_INSTALL : true,

  /**
   * Initializes the object.
   */
  init : function(aReason) {
    Components.utils.import("resource://gre/modules/Services.jsm");
    Components.utils.import("resource://gre/modules/AddonManager.jsm");

    // No windows are opened yet at startup.
    if (APP_STARTUP == aReason) {
      let that = this;
      let observer = {
          observe : function(aSubject, aTopic, aData) {
            if ("domwindowopened" == aTopic) {
              Services.ww.unregisterNotification(observer);

              let window = aSubject.QueryInterface(Ci.nsIDOMWindow);
              // wait for the window to load so that the prompt appears on top.
              window.addEventListener(
                "load", function() { that.run(); }, false);
            }
          }
        };

      Services.ww.registerNotification(observer);
    } else {
      this.run();
    }
  },

  /**
   * Runs the installer.
   */
  run : function() {
    try {
      let permCount = this.PERMISSIONS.length;
      let hasLocalFiles = false;
      let domain;

      if ((0 < permCount) &&
          (this.SILENT_INSTALL || this._showWarningMessage())) {
        // read all data.
        for (let i = 0 ; i < permCount ; i++) {
          domain = this.PERMISSIONS[i];

          if ("string" == typeof(domain) && (0 < domain.length)) {
            this._add(domain);
          }
        }
      }
    } catch (e) {
      this._showAlert("Unexpected error:\n" + e);
    }

    try {
      // auto remove.
      this._suicide();
    } catch (e) {
      this._showAlert(
        "Unexpected error:\n" + e + "\nPlease uninstall this add-on.");
    }
  },

  /**
   * Add a permission to the remote XUL list.
   * @param aPermission the permission to add. null to add all local files.
   */
  _add : function(aPermission) {
    try {
      if (this.LOCAL_FILES != aPermission) {
        let uri;

        if ((0 != aPermission.indexOf("http://")) &&
            (0 != aPermission.indexOf("https://"))) {
          aPermission = "http://" + aPermission;
        }

        uri = Services.io.newURI(aPermission, null, null);
        Services.perms.add(uri, this.ALLOW_REMOTE_XUL, this.ALLOW);
      } else {
        Services.prefs.setBoolPref(this.LOCAL_FILE_PREF, true);
      }
    } catch (e) {
      this._showAlert(
        "Unexpected error adding permission \'" + aPermission + "\':\n" + e);
    }
  },

  /**
   * Shows a warning indicating that remote XUL should be used carefully and
   * asking the user if its OK to proceed.
   * @return true if the user accepts the dialog, false if the user rejects it.
   */
  _showWarningMessage : function() {
    let title =
      ((0 < this.TITLE_LOCALIZED.length) ? this.TITLE_LOCALIZED : this.TITLE);
    let content =
      ((0 < this.WARNING_LOCALIZED.length) ? this.WARNING_LOCALIZED :
       this.WARNING);
    let permCount = this.PERMISSIONS.length;

    content += "\n";

    for (let i = 0 ; i < permCount ; i++) {
      content += "\n" + this.PERMISSIONS[i];
    }

    return Services.prompt.confirm(null, title, content);
  },

  /**
   * Shows an alert message with the given content.
   * @param aContent the content of the message to display.
   */
  _showAlert : function(aContent) {
    let title =
      ((0 < this.TITLE_LOCALIZED.length) ? this.TITLE_LOCALIZED : this.TITLE);

    Services.prompt.alert(null, title, aContent);
  },
  /**
   * Uninstall this add-on.
   */
  _suicide : function() {
    AddonManager.getAddonByID(
      "07018e1f-09af-415b-af5d-0c5c4e353256@rxm.xulforge.com",
      function(aAddon) {
        aAddon.uninstall();
      });
  }
};';
  return $jsOutFile;
}

?>