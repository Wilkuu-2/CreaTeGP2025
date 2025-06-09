<?php
namespace App\Enums;

enum Operator: string {
    case CHECK = "check";
    case LINK = "link"; // Linked to milestone
    case LTE = "lte";
    case GTE = "gte";
    case LT = "lt";
    case GT = "gt";
    case INP = "inp"; // Input (satisfied if entered)
    case OPT = "optional"; // Optional (Always satisfied)
}
