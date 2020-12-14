<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Языковые ресурсы для проверки значений
    |--------------------------------------------------------------------------
    |
    | Последующие языковые строки содержат сообщения по-умолчанию, используемые
    | классом, проверяющим значения (валидатором). Некоторые из правил имеют
    | несколько версий, например, size. Вы можете поменять их на любые
    | другие, которые лучше подходят для вашего приложения.
    |
    */

    'accepted'        => 'Вы должны принять ":attribute".',
    'active_url'      => 'Поле содержит недействительный URL.',
    'after'           => 'В поле должна быть дата после :date.',
    'after_or_equal'  => 'В поле должна быть дата после или равняться :date.',
    'alpha'           => 'Поле может содержать только буквы.',
    'alpha_dash'      => 'Поле может содержать только буквы, цифры, дефис и нижнее подчеркивание.',
    'alpha_num'       => 'Поле может содержать только буквы и цифры.',
    'array'           => 'Поле должно быть массивом.',
    'before'          => 'В поле должна быть дата до :date.',
    'before_or_equal' => 'В поле должна быть дата до или равняться :date.',
    'between'         => [
        'numeric' => 'Поле должно быть между :min и :max.',
        'file'    => 'Размер файла в поле должен быть между :min и :max Килобайт(а).',
        'string'  => 'Количество символов в поле должно быть между :min и :max.',
        'array'   => 'Количество элементов в поле должно быть между :min и :max.',
    ],
    'boolean'        => 'Поле должно иметь значение логического типа.',
    'confirmed'      => 'Поле не совпадает с подтверждением.',
    'date'           => 'Поле не является датой.',
    'date_equals'    => 'Поле должно быть датой равной :date.',
    'date_format'    => 'Поле не соответствует формату :format.',
    'different'      => 'Поля и :other должны различаться.',
    'digits'         => 'Длина цифрового поля должна быть :digits.',
    'digits_between' => 'Длина цифрового поля должна быть между :min и :max.',
    'dimensions'     => 'Поле имеет недопустимые размеры изображения.',
    'distinct'       => 'Поле содержит повторяющееся значение.',
    'email'          => 'Поле должно быть действительным электронным адресом.',
    'ends_with'      => 'Поле должно заканчиваться одним из следующих значений: :values',
    'exists'         => 'Выбранное значение некорректно.',
    'file'           => 'Поле должно быть файлом.',
    'filled'         => 'Поле обязательно для заполнения.',
    'gt'             => [
        'numeric' => 'Поле должно быть больше :value.',
        'file'    => 'Размер файла в поле должен быть больше :value Килобайт(а).',
        'string'  => 'Количество символов в поле должно быть больше :value.',
        'array'   => 'Количество элементов в поле должно быть больше :value.',
    ],
    'gte' => [
        'numeric' => 'Поле должно быть больше или равно :value.',
        'file'    => 'Размер файла в поле должен быть больше или равен :value Килобайт(а).',
        'string'  => 'Количество символов в поле должно быть больше или равно :value.',
        'array'   => 'Количество элементов в поле должно быть больше или равно :value.',
    ],
    'image'    => 'Поле должно быть изображением.',
    'in'       => 'Выбранное значение для ошибочно.',
    'in_array' => 'Поле не существует в :other.',
    'integer'  => 'Поле должно быть целым числом.',
    'ip'       => 'Поле должно быть действительным IP-адресом.',
    'ipv4'     => 'Поле должно быть действительным IPv4-адресом.',
    'ipv6'     => 'Поле должно быть действительным IPv6-адресом.',
    'json'     => 'Поле должно быть JSON строкой.',
    'lt'       => [
        'numeric' => 'Поле должно быть меньше :value.',
        'file'    => 'Размер файла в поле должен быть меньше :value Килобайт(а).',
        'string'  => 'Количество символов в поле должно быть меньше :value.',
        'array'   => 'Количество элементов в поле должно быть меньше :value.',
    ],
    'lte' => [
        'numeric' => 'Поле должно быть меньше или равно :value.',
        'file'    => 'Размер файла в поле должен быть меньше или равен :value Килобайт(а).',
        'string'  => 'Количество символов в поле должно быть меньше или равно :value.',
        'array'   => 'Количество элементов в поле должно быть меньше или равно :value.',
    ],
    'max' => [
        'numeric' => 'Поле не может быть более :max.',
        'file'    => 'Размер файла в поле не может быть более :max Килобайт(а).',
        'string'  => 'Количество символов в поле не может превышать :max.',
        'array'   => 'Количество элементов в поле не может превышать :max.',
    ],
    'mimes'     => 'Поле должно быть файлом одного из следующих типов: :values.',
    'mimetypes' => 'Поле должно быть файлом одного из следующих типов: :values.',
    'min'       => [
        'numeric' => 'Поле должно быть не менее :min.',
        'file'    => 'Размер файла в поле должен быть не менее :min Килобайт(а).',
        'string'  => 'Количество символов в поле должно быть не менее :min.',
        'array'   => 'Количество элементов в поле должно быть не менее :min.',
    ],
    'not_in'               => 'Выбранное значение ошибочно.',
    'not_regex'            => 'Выбранный формат ошибочный.',
    'numeric'              => 'Поле должно быть числом.',
    'password'             => 'Неверный пароль.',
    'present'              => 'Поле должно присутствовать.',
    'regex'                => 'Поле имеет ошибочный формат.',
    'required'             => 'Поле обязательно для заполнения.',
    'required_if'          => 'Поле обязательно для заполнения, когда :other равно :value.',
    'required_unless'      => 'Поле обязательно для заполнения, когда :other не равно :values.',
    'required_with'        => 'Поле обязательно для заполнения, когда :values указано.',
    'required_with_all'    => 'Поле обязательно для заполнения, когда :values указано.',
    'required_without'     => 'Поле обязательно для заполнения, когда :values не указано.',
    'required_without_all' => 'Поле обязательно для заполнения, когда ни одно из :values не указано.',
    'same'                 => 'Значения полей ":attribute" и :other должны совпадать.',
    'size'                 => [
        'numeric' => 'Поле должно быть равным :size.',
        'file'    => 'Размер файла в поле должен быть равен :size Килобайт(а).',
        'string'  => 'Количество символов в поле должно быть равным :size.',
        'array'   => 'Количество элементов в поле должно быть равным :size.',
    ],
    'starts_with' => 'Поле должно начинаться из одного из следующих значений: :values',
    'string'      => 'Поле должно быть строкой.',
    'timezone'    => 'Поле должно быть действительным часовым поясом.',
    'unique'      => 'Такое значение поля уже существует.',
    'uploaded'    => 'Загрузка поля не удалась.',
    'url'         => 'Поле имеет ошибочный формат.',
    'uuid'        => 'Поле должно быть корректным UUID.',

    /*
    |--------------------------------------------------------------------------
    | Собственные языковые ресурсы для проверки значений
    |--------------------------------------------------------------------------
    |
    | Здесь Вы можете указать собственные сообщения для атрибутов.
    | Это позволяет легко указать свое сообщение для заданного правила атрибута.
    |
    | http://laravel.com/docs/validation#custom-error-messages
    | Пример использования
    |
    |   'custom' => [
    |       'email' => [
    |           'required' => 'Нам необходимо знать Ваш электронный адрес!',
    |       ],
    |   ],
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'email' => [
            'unique' => 'Данный email уже зарегистрирован'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Собственные названия атрибутов
    |--------------------------------------------------------------------------
    |
    | Последующие строки используются для подмены программных имен элементов
    | пользовательского интерфейса на удобочитаемые. Например, вместо имени
    | поля "email" в сообщениях будет выводиться "электронный адрес".
    |
    | Пример использования
    |
    |   'attributes' => [
    |       'email' => 'электронный адрес',
    |   ],
    |
    */

    'attributes' => [
        'name'                  => 'Имя',
        'username'              => 'Никнейм',
        'email'                 => 'E-Mail адрес',
        'first_name'            => 'Имя',
        'last_name'             => 'Фамилия',
        'password'              => 'Пароль',
        'password_confirmation' => 'Подтверждение пароля',
        'city'                  => 'Город',
        'country'               => 'Страна',
        'address'               => 'Адрес',
        'phone'                 => 'Телефон',
        'mobile'                => 'Моб. номер',
        'age'                   => 'Возраст',
        'sex'                   => 'Пол',
        'gender'                => 'Пол',
        'day'                   => 'День',
        'month'                 => 'Месяц',
        'year'                  => 'Год',
        'hour'                  => 'Час',
        'minute'                => 'Минута',
        'second'                => 'Секунда',
        'title'                 => 'Наименование',
        'content'               => 'Контент',
        'description'           => 'Описание',
        'excerpt'               => 'Выдержка',
        'date'                  => 'Дата',
        'time'                  => 'Время',
        'available'             => 'Доступно',
        'size'                  => 'Размер',
    ],
];
