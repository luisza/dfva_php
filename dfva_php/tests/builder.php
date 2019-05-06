<?php

require_once dirname(__FILE__).'/utils.php';

function build_authentication($name){
    global $AUTHENTICATION_RESPONSE_TABLE;
    echo printf('
class %s (unittest.TestCase):
    function setUp(self):
         pass

    function do_checks(self, identification):
         pass
', $name);
//    for identification in AUTHENTICATION_RESPONSE_TABLE:
    foreach ($AUTHENTICATION_RESPONSE_TABLE as $identification)
        printf('
    function test_auth_%s(self):
        self.do_checks("%s")', $identification, str_replace('-', '', $identification));
}


function build_test_document_python($name){
    global $DOCUMENT_FORMATS, $DOCUMENT_RESPONSE_TABLE;
    printf('
class %s (unittest.TestCase):
    function setUp(self):
         pass

    function do_checks(self, _format, identification):
         pass
', $name);
    foreach ($DOCUMENT_FORMATS as $format){
        foreach ($DOCUMENT_RESPONSE_TABLE as $identification){
            printf('
    function test_%s_%(identification_funcname)s(self):
        self.do_checks("%(docformat)s", "%(identification)s")',
                $format, str_replace('-', '', $identification),
                $format, $identification);
        }
    }
}