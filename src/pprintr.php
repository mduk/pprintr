<?php

function pprintr( $var, $indent=0, $indentStep = null ) {
    if ( $indentStep === null ) {
        if ( defined( 'PPRINTR_INDENT_STEP' ) ) {
            $indentStep = PPRINTR_INDENT_STEP;
        }
        else {
            $indentStep = 2;
        }
    }

    $str = '';

    if ( is_object( $var ) ) {
        $function = '_pprintr_object_' . str_replace( '\\', '_', get_class( $var ) );
        if ( !is_callable( $function ) ) {
            $function = '_pprintr_object';
        }
        $str = call_user_func_array( $function, array( $var, $indent, $indentStep ) );
    }
    else if ( is_array( $var ) ) {
        $str = _pprintr_array( $var, $indent, $indentStep );
    }
    else if ( is_string( $var ) ) {
        $str = _pprintr_string( $var, $indent, $indentStep );
    }
    else if ( is_numeric( $var ) ) {
        $str = _pprintr_numeric( $var, $indent, $indentStep );
    }
    else if ( is_bool( $var ) ) {
        $str = _pprintr_bool( $var, $indent, $indentStep );
    }
    else if ( is_null( $var ) ) {
        $str = _pprintr_null( $var, $indent, $indentStep );
    }

    return $str;
}

// -----------------------------------------------------------------------------
// String
// -----------------------------------------------------------------------------
function _pprintr_string( $var, $indent, $indentStep ) {
    return "\033[92m\"{$var}\"\033[0m";
}

// -----------------------------------------------------------------------------
// Numeric
// -----------------------------------------------------------------------------
function _pprintr_numeric( $var, $indent, $indentStep ) {
    return "\033[96m{$var}\033[0m";
}

// -----------------------------------------------------------------------------
// Boolean
// -----------------------------------------------------------------------------
function _pprintr_bool( $var, $indent, $indentStep ) {
    return ( $var ? "\033[92mTRUE\033[0m" : "\033[91mFALSE\033[0m" );
}

// -----------------------------------------------------------------------------
// Null
// -----------------------------------------------------------------------------
function _pprintr_null( $var, $indent, $indentStep ) {
    return "\033[90mNULL\033[0m";
}

// -----------------------------------------------------------------------------
// Array
// -----------------------------------------------------------------------------
function _pprintr_array( $var, $indent, $indentStep ) {
    $count = count( $var );
    $str = "\033[95m(array:{$count})\033[0m\n";
    $pad = str_repeat( ' ', ( $indent + $indentStep ) );
    $elems = [];
    foreach ( $var as $ek => $ev ) {
        $k = "\033[94m[\033[0m\033[1m{$ek}\033[0m\033[94m]\033[0m";
        $arrow = "\033[32m=>\033[0m";
        $ev = pprintr( $ev, ( $indent + $indentStep ), $indentStep );
        $elems[] = "{$pad}{$k} {$arrow} {$ev}";
    }
    $str .= implode( "\n", $elems );
    return $str;
}

// -----------------------------------------------------------------------------
// Object
// -----------------------------------------------------------------------------
function _pprintr_object( $var, $indent, $indentStep ) {
    $pad = str_repeat( ' ', ( $indent + $indentStep ) );
    $reflection = new ReflectionObject( $var );
    $elems = [];
    $includeProtected = defined( 'PPRINTR_INCLUDE_PROTECTED' ) && constant( 'PPRINTR_INCLUDE_PROTECTED' );

    foreach ( $reflection->getProperties() as $property ) {
        if ( $property->isPublic() || ( !$property->isPublic() && $includeProtected ) ) {
            $elems[] = _pprintr_object_ReflectionProperty( $property, $var, $indent, $indentStep );
        }
    }

    $name = $reflection->getName();
    $elems = $pad . implode( "\n{$pad}", $elems );
    $string = "\033[93m(object:{$name})\033[0m\n{$elems}";
    return $string;
}

function _pprintr_object_ReflectionProperty( ReflectionProperty $property, $var, $indent, $indentStep ) {
    $modifiers = array();
    $property->setAccessible( true );

    if ( defined( 'PPRINTR_INCLUDE_PROTECTED' ) && PPRINTR_INCLUDE_PROTECTED ) {
        $property->isPublic() && $modifiers[] = "\033[92mpublic\033[0m";
        $property->isProtected() && $modifiers[] = "\033[91mprotected\033[0m";
        $property->isPrivate() && $modifiers[] = "\033[91mprivate\033[0m";
    }

    $property->isStatic() && $modifiers[] = "static";

    $modifiers = implode( ' ', $modifiers );

    $name = $property->getName();
    $value = pprintr( $property->getValue( $var ), ( $indent + $indentStep ), $indentStep );

    $str = "\033[0m\033[1m{$name}\033[0m => {$value}";
    if ( $modifiers != [] ) {
        $str = "\033[0m{$modifiers}\033[1m {$str}";
    }

    return $str;
}

