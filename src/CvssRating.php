<?php

declare(strict_types=1);

namespace TypistTech\WordfenceApi;

enum CvssRating: string
{
    case Critical = 'Critical';
    case High = 'High';
    case Medium = 'Medium';
    case Low = 'Low';
    case None = 'None';
}
