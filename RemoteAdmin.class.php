<?php

class RemoteAdmin {
    protected $server_adress;
    protected $port;
    protected $access_password;
    
    protected $data;
    
    public function __construct()
    {
        require_once('config.php');
        $this->server_adress = $server_adress;
        $this->port = $port;
        $this->access_password = $access_password;
        
        $this->data = file_get_contents('RemoteAdmin.json');
    }
    
    public function get_data($array = false,$depth=512)
    {
        return json_decode($this->data,$array,$depth);
    }
    
    function isValidGuid($guid)
    {
       // return !empty($guid) && preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/i', $guid);
    }
    
    function SendCommand($command, $params=array())
    {
    /*
        This function is taken from RemoteAdmin.php
        
        Copyright (c) 2008, The New World Grid Regents http://www.newworldgrid.com and Contributors 
        All rights reserved.

        Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

        * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
        * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
        * Neither the name of the New World Grid nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

        THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

    */
        $paramsNames = array_keys($params);
        $paramsValues = array_values($params);
         
        // Building the XML data to pass to RemoteAdmin through XML-RPC ;)
         
        $xml = '<methodCall>
                    <methodName>' . htmlspecialchars($command) . '</methodName>
                        <params>
                            <param>
                                <value>
                                    <struct>
                                        <member>
                                            <name>password</name>
                                            <value><string>' . htmlspecialchars($this->access_password) . '</string></value>
                                        </member>';
        if (count($params) != 0)
        {
            for ($p = 0; $p < count($params); $p++)
            {
                $xml .= '<member><name>' . htmlspecialchars($paramsNames[$p]) . '</name>';
                $xml .= '<value>' . htmlspecialchars($paramsValues[$p]) . '</value></member>';
            }
                 
        }
                                                         
        $xml .= '                   </struct>
                                </value>
                            </param>
                        </params>
                </methodCall>';

        //echo $xml;              
        // Now building headers and sending the data ;)
        $host = $this->server_adress;
        $port = $this->port;
        $timeout = 5; // Timeout in seconds
         
        error_reporting(0);
         
        $fp = fsockopen($host, $port, $errno, $errstr, $timeout);
        if (!$fp)
        {
            echo "NO FLE POINTER !".$host.$port.$errstr;
            return FALSE; // If contacting host timeouts or impossible to create the socket, the method returns FALSE
        }
        else
        {
            fputs($fp, "POST / HTTP/1.1\r\n");
            fputs($fp, "Host: $host\r\n");
            fputs($fp, "Content-type: text/xml\r\n");
            fputs($fp, "Content-length: ". strlen($xml) ."\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $xml);
            $res = "";
            while(!feof($fp)) {
                $res .= fgets($fp, 128);
            }
            fclose($fp);
            $response = substr($res, strpos($res, "\r\n\r\n"));;
                             
            // Now parsing the XML response from RemoteAdmin ;)
            //print_r($response);
            $result = array();
            if (preg_match_all('#<name>(.+)</name><value><(string|int|boolean|i4)>(.*)</\2></value>#U', $response, $regs, PREG_SET_ORDER)) {
                foreach($regs as $key=>$val) {
                    $result[$val[1]] = $val[3];
                }
            }
            return $result;
        }
    }

}
