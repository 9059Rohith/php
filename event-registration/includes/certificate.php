<?php
declare(strict_types=1);

function certificate_id_for_registration(int $registrationId): string
{
    return 'CERT-' . str_pad((string) $registrationId, 6, '0', STR_PAD_LEFT);
}
