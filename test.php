#!/usr/bin/env php
<?php

namespace Mduk\Foo {
  class Bar {
    public $foo = 'bar';
    protected $bar = 'baz';
    private $baz = 'qha';
  }
}

namespace {

  require_once 'src/pprintr.php';

  function _pprintr_object_User( $var, $indent, $indentStep ) {
    return "\033[93m(User: #{$var->id} {$var->username})\033[0m";
  }

  class User {
    public $id = 28239;
    public $username = 'dan.kendell@example.com';
  }

  $array = [
    'null' => null,
    'true' => true,
    'false' => false,
    'integer' => 123,
    'float' => 123.45,
    'string' => "hello world",
    'array' => [ 1, 2, 3 ]
  ];
  $obj = (object) $array;
  $array['object'] = $obj;
  $array['object2'] = new User;
  $array['object3'] = new Mduk\Foo\Bar;

  define( 'PPRINTR_INDENT_STEP', 4 );
  define( 'PPRINTR_INCLUDE_PROTECTED', true );

  echo "§";
  echo pprintr( null ), "§\n\n§";
  echo pprintr( true ), "§\n\n§";
  echo pprintr( false ), "§\n\n§";
  echo pprintr( 123 ), "§\n\n§";
  echo pprintr( 123.45 ), "§\n\n§";
  echo pprintr( "hello world" ), "§\n\n§";
  echo pprintr( [ null, true, false, 123, 123.45, "hello world" ] ), "§\n\n§";
  echo pprintr( $obj ), "§\n\n§";
  echo pprintr( $array ), "§\n\n";
}
