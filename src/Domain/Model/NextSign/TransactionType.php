<?php

namespace NextSignPHP\Domain\Model\NextSign;

enum TransactionType: string
{
    case ALL_SIGNERS = "allSigners";
    case BY_POSITION_ORDER = "byPositionOrder";
}
