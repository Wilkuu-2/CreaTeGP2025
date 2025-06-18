<?php
namespace App\Enums;

enum DType: string {
    case INT = "int";
    case DOUBLE = "double";
    case STRING = "string";
    case BOOL = "bool";
    case NONE = "none";
}
