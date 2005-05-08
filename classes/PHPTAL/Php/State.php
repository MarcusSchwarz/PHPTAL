<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
//  
//  Copyright (c) 2004-2005 Laurent Bedubourg
//  
//  This library is free software; you can redistribute it and/or
//  modify it under the terms of the GNU Lesser General Public
//  License as published by the Free Software Foundation; either
//  version 2.1 of the License, or (at your option) any later version.
//  
//  This library is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
//  Lesser General Public License for more details.
//  
//  You should have received a copy of the GNU Lesser General Public
//  License along with this library; if not, write to the Free Software
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//  
//  Authors: Laurent Bedubourg <lbedubourg@motion-twin.com>
//  

require_once 'PHPTAL/Php/Tales.php';

class PHPTAL_Php_State
{
    public function __construct()
    {
        $this->_debug      = false;
        $this->_talesMode  = 'tales';
        $this->_encoding   = 'UTF-8';
        $this->_outputMode = '';
    }

    public function setDebug($bool)
    {
        $old = $this->_debug;
        $this->_debug = $bool;
        return $old;
    }

    public function isDebugOn()
    {
        return $this->_debug;
    }

    public function setTalesMode($mode)
    {
        $old = $this->_talesMode;
        $this->_talesMode = $mode;
        return $old;
    }

    public function getTalesMode()
    {
        return $this->_talesMode;
    }

    public function setEncoding($enc)
    {
        $this->_encoding = $enc;
    }

    public function getEncoding()
    {
        return $this->_encoding;
    }

    public function setOutputMode($mode)
    {
        $this->_outputMode = $mode;
    }

    public function getOutputMode()
    {
        return $this->_outputMode;
    }

    public function evalTalesExpression($expression)
    {
        if ($this->_talesMode == 'php')
            return phptal_tales_php($expression);
        return phptal_tales($expression);
    }

    public function interpolateTalesVarsInString($string)
    {
        if ($this->_talesMode == 'tales'){
            return phptal_tales_string($string);
        }
        
        // replace ${var} found in expression
        while (preg_match('/\$\{([^\}]+)\}/ism', $string, $m)){
            list($ori, $exp) = $m;
            $php  = phptal_tales_php($exp);
            $repl = '\'.%s.\''; 
            $repl = sprintf($repl, $php, $this->_encoding);
            $string = str_replace($ori, $repl, $string);
        }
        return '\''.$string.'\'';
    }

    public function interpolateTalesVarsInHtml($src)
    {
        if ($this->_talesMode == 'tales'){
            return preg_replace(
                '/\$\{([a-z0-9\/_]+)\}/ism', 
                '<?php echo htmlspecialchars('
                .'phptal_path($ctx, \'$1\'), ENT_QUOTES, \''.$this->_encoding.'\' '
                .') ?>',
                $src
            );
        }

        while (preg_match('/\${(structure )?([^\}]+)\}/ism', $src, $m)){
            list($ori, $struct, $exp) = $m;
            $php  = phptal_tales_php($exp);
            // when structure keyword is specified the output is not html 
            // escaped
            if ($struct){
                $repl = '<?php echo '.$php.'; ?>';
            }
            else {
                $repl = '<?php echo '.$this->htmlchars($php).'; ?>';
            }
            $src  = str_replace($ori, $repl, $src);
        }
        return $src;
    }

    public function htmlchars($php)
    {
        return 'htmlspecialchars('.$php.', ENT_QUOTES, \''.$this->_encoding.'\')';
    }
        
    private $_debug;
    private $_talesMode;
    private $_encoding;
    private $_outputMode;
}

?>