<?php

function generateTrackingNumber(): string
{
    return 'TRK' . date('YmdHis') . random_int(100, 999);
}

function validShipmentStatuses(): array
{
    return [
        'Pending',
        'Picked Up',
        'In Transit',
        'Out for Delivery',
        'Delivered',
        'Delayed'
    ];
}