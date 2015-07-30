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

























namespace Old {

class PrettyPrinter {

	protected $printers = array(
		'bool' => 'BoolPrinter',
		'array' => 'ArrayPrinter'
	);

	public function doPrint( $obj, $indent = 2 ) {
		$type = $this->getType( $obj );
		$printer = $this->getPrinter( $type );
		$struct = $printer->pprint( $obj );
		return $this->render( $struct[0], $struct[1], 0 );
	}

	protected function render( $line, $indentedLines, $level ) {
		$rendered = $line;
		$level++;
		foreach ( $indentedLines as $indentedLine ) {
			$rendered .= $this->render( $indentedLine[0], $indentedLine[1], $level );
		}
		return $rendered;
	}

	protected function getPrinter( $type ) {
		if ( !isset( $this->printers[ $type ] ) ) {
			throw new Exception( "No printer for type: {$type}" );
		}

		$class = $this->printers[ $type ];
		return new $class;
	}

	protected function getType( $obj ) {
		if ( is_bool( $obj ) ) {
			return 'bool';
		}

		if ( is_array( $obj ) ) {
			return 'array';
		}

		if ( is_string( $obj ) ) {
			return 'string';
		}

		if ( is_object( $obj ) ) {
			return get_class( $obj );
		}

		throw new Exception( "Unknown type" );
	}
}

class BoolPrinter {
	public function pprint( $bool ) {
		return array( ( $var ? "\033[91mTRUE\033[0m" : "\033[91mFALSE\033[0m" ), array() );
	}
}

class ArrayPrinter {
	public function pprint( $array ) {
		$lines = array();
		foreach ( $array as $k => $v ) {
			$lines[] = "[{$k}] => " . parent::pprint( $v );
		}
		$count = count( $array );
		return array( "array({$count})", $lines );
	}
}

#$printer = new PrettyPrinter;
#$printer->pprint( array( 'keyOne' => true ) );
#$printer->doPrint( true );

}
