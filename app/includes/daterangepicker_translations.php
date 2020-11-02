<?php

/* Translations for the datepicker external library */
return [
    'format' => 'YYYY-MM-DD',
    'separator' => ' - ',
    'applyLabel' => \Altum\Language::get()->global->date->apply,
    'cancelLabel' => \Altum\Language::get()->global->date->cancel,
    'fromLabel' => \Altum\Language::get()->global->date->from,
    'toLabel' => \Altum\Language::get()->global->date->to,
    'customRangeLabel' => \Altum\Language::get()->global->date->custom,
    'weekLabel' => 'W',
    'daysOfWeek' => [
        \Altum\Language::get()->global->date->short_days->{7},
        \Altum\Language::get()->global->date->short_days->{1},
        \Altum\Language::get()->global->date->short_days->{2},
        \Altum\Language::get()->global->date->short_days->{3},
        \Altum\Language::get()->global->date->short_days->{4},
        \Altum\Language::get()->global->date->short_days->{5},
        \Altum\Language::get()->global->date->short_days->{6}
    ],
    'monthNames' => [
        \Altum\Language::get()->global->date->long_months->{1},
        \Altum\Language::get()->global->date->long_months->{2},
        \Altum\Language::get()->global->date->long_months->{3},
        \Altum\Language::get()->global->date->long_months->{4},
        \Altum\Language::get()->global->date->long_months->{5},
        \Altum\Language::get()->global->date->long_months->{6},
        \Altum\Language::get()->global->date->long_months->{7},
        \Altum\Language::get()->global->date->long_months->{8},
        \Altum\Language::get()->global->date->long_months->{9},
        \Altum\Language::get()->global->date->long_months->{10},
        \Altum\Language::get()->global->date->long_months->{11},
        \Altum\Language::get()->global->date->long_months->{12},
    ],
    'firstDay' => 1
];
