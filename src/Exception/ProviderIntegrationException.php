<?php

namespace CommissionCalc\Exception;

/**
 * Happens when data provider works with external API
 * and cannot connect to host (or another data source) or parse its data in runtime.
 */
class ProviderIntegrationException extends RuntimeException
{
}
