<?php
declare(strict_types=1);

function send_event_mail(string $to, string $subject, string $html): bool
{
    $headers = "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n";

    return mail($to, $subject, $html, $headers);
}
