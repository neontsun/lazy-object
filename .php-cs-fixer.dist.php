<?php

declare(strict_types = 1);

$finder = (new \PhpCsFixer\Finder())
    ->in(__DIR__ . '/src/')
    ->in(__DIR__ . '/tests/')
    ->notPath([
        'Shared/src/events.php',
    ]);

return (new \PhpCsFixer\Config())
    ->setParallelConfig(\PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setFormat('txt')
    ->setUsingCache(true)
    ->setCacheFile(__DIR__ . '/var/.php-cs-fixer.cache')
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules([
        // alias
        // конвертирует array_push в array[]
        'array_push' => true,
        // конвертирует pow($a, 2) в $a** 2
        'pow_to_exponentiation' => true,
        // Вместо алиасов языка должны использоваться конструкции (die -> exit ...)
        'no_alias_language_construct_call' => true,
        
        
        // array_notation
        // Синтаксис массива [] или array
        'array_syntax' => [
            'syntax' => 'short',
        ],
        // В массиве не должно быть пробелов перед запятой
        'no_whitespace_before_comma_in_array' => true,
        // $array{index} -> $array[index]
        'normalize_index_brace' => true,
        // return [1, 2, 3] -> yield from [1, 2, 3]
        'return_to_yield_from' => true,
        // Удаляет лишние пробелы в начале и конце массива
        'trim_array_spaces' => true,
        
        
        // attribute_notation
        // #[Attribute()] -> #[Attribute]
        'attribute_empty_parentheses' => true,
        
        
        // basic
        // Позиции фигурных скобок
        'braces_position' => [
            'allow_single_line_empty_anonymous_classes' => false,
            'allow_single_line_anonymous_functions' => false,
        ],
        // Каждый вызов statement'а (разделенного ;) на новой строке
        'no_multiple_statements_per_line' => true,
        // Если структура однострочная, то не должно быть завершающей запятой
        'no_trailing_comma_in_singleline' => true,
        // Разделять числа 1123121 на 1_123_121
        'numeric_literal_separator' => [
            'strategy' => 'use_separator',
        ],
        // Пустое тело класса, интерфейса, типажа, перечисления или функции должно быть сокращено до {}
        // и помещено в ту же строку, что и предыдущий символ, через один пробел.
        'single_line_empty_body' => true,
        
        
        // casing
        // Ссылка на базовый класс в правильном регистре
        'class_reference_name_casing' => true,
        // false, true, null в правильном регистре
        'constant_case' => true,
        // Все ключевые слова в нижнем регистре
        'lowercase_keywords' => true,
        // Статичные ссылки в нижнем регистре
        'lowercase_static_reference' => true,
        // Магические константы в uppercase
        'magic_constant_casing' => true,
        // Магические методы в lowercase
        'magic_method_casing' => true,
        // Нативные функции в lowercase
        'native_function_casing' => true,
        // Нативные типы в lowercase
        'native_type_declaration_casing' => true,
        
        
        // cast_notation
        // Расстояние между приведением и переменной
        'cast_spaces' => true,
        // Приведение типов в правильном регистре
        'lowercase_cast' => true,
        // Перевод старого приведения типов на новый синтаксис
        'modernize_types_casting' => true,
        // Избавление от короткого (!!) приведения к логическому типу
        'no_short_bool_cast' => true,
        // Переменные должны быть установлены на null вместо использования (unset) приведения.
        'no_unset_cast' => true,
        // Коротка форма типов данных при приведении типов
        'short_scalar_cast' => true,
        
        
        // class_notation
        // Настраивает кол-во пробелов между атрибутами класса
        'class_attributes_separation' => ['elements' => ['property' => 'one', 'const' => 'one', 'method' => 'one', ],],
        // Один пробел в объявлении класса, трейта, имплементации
        'class_definition' => true,
        // Все публичные методы абстрактного класса final
        'final_public_method_for_abstract_class' => true,
        // Удаляет лишнюю строку после фигурной скобки открытия класса
        'no_blank_lines_after_class_opening' => true,
        // Удаляет final у методов, где это возможно
        'no_unneeded_final_method' => true,
        // Упорядочивает методы, свойства, трейти, константы класса (public, protected, private)
        'ordered_class_elements' => true,
        // Сортирует интерфейсы при реализации
        'ordered_interfaces' => [
            'direction' => 'ascend',
            'order' => 'alpha',
        ],
        // Сортирует типы данных
        'ordered_types' => ['null_adjustment' => 'always_first',],
        // readonly из phpdoc в ключевое слово
        'phpdoc_readonly_class_comment_to_keyword' => true,
        // Преобразует защищенные переменные и методы в частные, где это возможно.
        'protected_to_private' => true,
        // В текущем классе заменяет все упоминания класса на self
        'self_accessor' => true,
        // Внутри enum или final/anonymous класса заменяет static на self
        'self_static_accessor' => true,
        // Каждое поле класса или константа должна быть с новой строки со своим оператором
        'single_class_element_per_statement' => true,
        // Каждый трейт со своим оператором use на новой строке
        'single_trait_insert_per_statement' => true,
        // Для полей класса, констант и методов модификаторы доступа обязательны
        // final/abstract перед модификатором доступа, а static после модификатора доступа
        'visibility_required' => true,
        
        
        // comment
        // Комментарии с аннотациями должны быть в docblock
        'comment_to_phpdoc' => true,
        // Удаляет пустые комментарии
        'no_empty_comment' => true,
        // В конце phpdoc-комментария не должно быть пробелов
        'no_trailing_whitespace_in_comment' => true,
        // Однострочные комментарии должны иметь правильный интервал
        'single_line_comment_spacing' => true,
        // Однострочные комментарии и многострочные с одной строкой должны использовать //
        'single_line_comment_style' => true,
        
        
        // constant_notation
        // Ссылается на корень \ у нативных констант
        'native_constant_invocation' => true,
        
        
        //control_structure
        // Тело каждой управляющей структуры должно быть заключено в фигурные скобки
        'control_structure_braces' => true,
        // Ключевое слово для управляющей конструкции продолжения на этой же строке ( } else {, } catch() { )
        'control_structure_continuation_position' => true,
        // elseif слитно
        'elseif' => true,
        // Include/Require и путь к файлу должны быть разделены одним пробелом. Путь к файлу не должен заключаться в круглые скобки.
        'include' => true,
        // Комментарий no break, если намерено тело case в switch пустое
        'no_break_comment' => true,
        // Лишние elseif на if
        'no_superfluous_elseif' => true,
        // Удаляет ненужные фигурные скобки
        'no_unneeded_braces' => true,
        // Удаляет ненужные круглые скобки
        'no_unneeded_control_parentheses' => true,
        // Удаляет ненужные else
        'no_useless_else' => true,
        // Упрощает return, где одно условие и два return'а
        'simplified_if_return' => true,
        // в switch у case должно быть :, а не ;
        'switch_case_semicolon_to_colon' => true,
        // Удаляет лишние пробелы у case в switch
        'switch_case_space' => true,
        // Добавляет запятую у последнего элемента в массиве, аргументах и параметрах
        'trailing_comma_in_multiline' => [
            'elements' => [
                'arguments',
                'arrays',
                'match',
                'parameters',
                'array_destructuring',
            ],
        ],
        'yoda_style' => true,
        
        
        // function_notation
        // Вложенные dirname конвертирует в dirname($path, 3)
        'combine_nested_dirname' => true,
        // b и t в флагах всегда в конце
        'fopen_flag_order' => true,
        // добавляет флаг b
        'fopen_flags' => true,
        // Удаляет пробелы между названием метода и его параметрами
        'function_declaration' => [
            'closure_fn_spacing' => 'none',
            'closure_function_spacing' => 'none',
        ],
        // Правильный implode
        'implode_call' => true,
        // Лямбда не должна импортировать переменный, которые не использует 
        'lambda_not_used_import' => true,
        // 
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'attribute_placement' => 'standalone',
        ],
        // Добавляет начальный \ к вызову функций для ускорения работы
        'native_function_invocation' => true,
        // Удаляет пробелы после имени функции
        'no_spaces_after_function_name' => true,
        // Не должно быть аргументов со значениями по умолчанию перед аргументами
        // без значения по умолчанию
        'no_unreachable_default_argument_value' => true,
        // Удаляет sprintf без аргументов
        'no_useless_sprintf' => true,
        // Помечает параметр как nullable, если значение по умолчание null
        'nullable_type_declaration_for_default_null_value' => true, 
        // Пробел после возвращаемого типа метода
        'return_type_declaration' => true,
        // Если в анонимной функции нет ссылки на $this, то она должна быть помечена как static
        'static_lambda' => true,
        // Возвращение void, если не указано типа (приоритет на @return)
        'void_return' => true,
        
        
        // import
        // Импортирование глобального пространства имен у методов и классов
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        // Удаление начальных слешей в use
        'no_leading_import_slash' => true,
        // Импорт не должен иметь псевдонима с тем же именем
        'no_unneeded_import_alias' => true,
        // Удаляет неиспользуемые use
        'no_unused_imports' => true,
        // Сортирует импорты
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => [
                'const', 
                'class', 
                'function',
            ],
        ],
        // Свой оператор для каждого импорта, с новой строки
        'single_import_per_statement' => true,
        // Пустая строка после импортов
        'single_line_after_imports' => true,
        
        
        // language_construct
        // isset && несколько раз должно вызываться за раз isset(,)
        'combine_consecutive_issets' => true,
        // unset(); unset(); -> unset(,)
        'combine_consecutive_unsets' => true,
        // Оператор объявления знака равенства должен быть окружен пробелами или не соответствовать конфигурации.
        'declare_equal_normalize' => [
            'space' => 'single',
        ],
        // У круглых скобок declare не должно быть пробелов
        'declare_parentheses' => true,
        // Заменяет dirname(__FILE__) на __DIR__
        'dir_constant' => true,
        // Оператор игнорирования ошибок @
        'error_suppression' => [
            'mute_deprecation_error' => false,
            'noise_remaining_usages' => true,
        ],
        // Добавьте фигурные скобки к косвенным переменным, чтобы их было легче понять.
        'explicit_indirect_variable' => true,
        // Замените вызовы основных функций, возвращающих константы, на константы.
        'function_to_constant' => [
            'functions' => [
                'get_called_class', 
                'get_class', 
                'get_class_this', 
                'php_sapi_name', 
                'phpversion', 
                'pi',
            ],
        ],
        // get_class() -> ::class
        'get_class_to_class_keyword' => true,
        // Заменяет is_null на === null
        'is_null' => true,
        // nullable у одно типа данных должно быть ?
        'nullable_type_declaration' => true,
        // Одиночный пробел вокруг языковых конструкций
        'single_space_around_construct' => [
            'constructs_preceded_by_a_single_space' => [
                'as', 
                'else', 
                'elseif', 
                'use_lambda',
            ],
        ],
        
        
        // list_notation
        // Синтаксис списка: [] вместо list()
        'list_syntax' => [
            'syntax' => 'short',
        ],
        
        
        // namespace_notation
        // Одна пустая строка после namespace
        'blank_line_after_namespace' => true,
        // Пробел перед namespace
        'blank_lines_before_namespace' => true,
        // Удаляет пробелы, комментарии в namespace
        'clean_namespace' => true,
        // Строка объявления пространства имен не должна содержать начальные пробелы.
        'no_leading_namespace_whitespace' => true,
        
        
        // operator
        // По возможности использует ??= вместо $foo = $foo ?? 1
        'assign_null_coalescing_to_coalesce_equal' => true,
        // Пробелы у бинарных операций
        'binary_operator_spaces' => true,
        // Один пробел между конкатенацией
        'concat_space' => [
            'spacing' => 'one',
        ],
        // &&, || вместо and, or
        'logical_operators' => true,
        // $a += 1, вместо $a = $a + 1
        'long_to_shorthand_operator' => true,
        // new у анонимных или именованных классов только с круглыми скобками
        'new_with_parentheses' => true,
        // Без пробелов у ::
        'no_space_around_double_colon' => true,
        // Не должно быть бесполезных конкатных операций.
        'no_useless_concat_operator' => true,
        // Не следует использовать бесполезный Null-safe оператор ?->
        'no_useless_nullsafe_operator' => true,
        // Логические операторы НЕ (!) должны иметь один конечный пробел.
        'not_operator_with_successor_space' => true,
        // Оператор -> без пробелов
        'object_operator_without_whitespace' => true,
        // Оператор при переносе строк всегда переносится на новую строку
        // return $a == 1
        //      || $a === 2
        'operator_linebreak' => true,
        // Заменяет <> на !=
        'standardize_not_equals' => true,
        // Пробелы у тернарного оператора
        'ternary_operator_spaces' => true,
        // Использовать нулевой оператор объединения ?? где это возможно.
        'ternary_to_null_coalescing' => true,
        // Унарные операторы следует размещать рядом со своими операндами.
        'unary_operator_spaces' => true,
        
        
        // php_tag
        // На строке с объявлением php-тега нет кода
        'blank_line_after_opening_tag' => true,
        // Код PHP должен использовать длинные теги <?php или короткие эхо-теги <?=, а не другие варианты тегов.
        'full_opening_tag' => true,
        // В файлах не должно быть закрывающего php-тега
        'no_closing_tag' => true,
        
        
        // phpdoc
        // Каждая строка комментария должна начинаться со звездочки и быть выровнена по левому краю
        'align_multiline_comment' => true,
        // Между докблоком и документируемым элементом не должно быть пустых строк.
        'no_blank_lines_after_phpdoc' => true,
        // Не должно быть пустых блоков phpdoc
        'no_empty_phpdoc' => true,
        // Форматирует phpdoc атрибуты
        'no_superfluous_phpdoc_tags' => [
            'allow_unused_params' => false,
            'remove_inheritdoc' => false,
            'allow_mixed' => true,
        ],
        // Выравнивает phpdoc аннотации
        'phpdoc_align' => [
            'align' => 'left',
        ],
        // array<T> вместо T[]
        'phpdoc_array_type' => true,
        // Блок phpdoc должен иметь тот же отступ, что и документируемый блок
        'phpdoc_indent' => true,
        // Изменяет блоки документов с однострочных на многострочные или переворачивает их. Работает только для констант классов, свойств и методов.
        'phpdoc_line_span' => true,
        // list<T> вместо array<T>
        'phpdoc_list_type' => true,
        // Удаляет @access
        'phpdoc_no_access' => true,
        // Удаляет @return void и @return null
        'phpdoc_no_empty_return' => true,
        // Удаляет @package и @subpackage
        'phpdoc_no_package' => true,
        // Порядок сортировка phpdoc полей
        'phpdoc_order' => [
            'order' => [
                'param',
                'return',
                'throws',
            ],
        ],
        // @param в соответствии с сигнатурой метода
        'phpdoc_param_order' => true,
        // Настраивает @return при возвращении self
        'phpdoc_return_self_reference' => true,
        // Скалярные типы всегда должны быть записаны в одной и той же форме
        'phpdoc_scalar' => true,
        // Одна строка @var PHPDoc должна иметь правильный интервал
        'phpdoc_single_line_var_spacing' => true,
        // Исправлен регистр тегов PHPDoc
        'phpdoc_tag_casing' => true,
        // Заставляет теги PHPDoc быть либо обычными аннотациями, либо встроенными
        'phpdoc_tag_type' => true,
        // PHPDoc должен начинаться и заканчиваться содержимым, исключая самую первую и последнюю строку докблоков
        'phpdoc_trim' => true,
        // Правильный регистр у типов в phpdoc
        'phpdoc_types' => true,
        // Порядок сортировки типов данных
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_first',
        ],
        // Корректное указание типов данных и переменных в phpdoc
        'phpdoc_var_annotation_correct_order' => true,
        // Удаляет пустые строки в phpdoc
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        
        
        // return_notation
        // В конце функции не должно быть пустого оператора return
        'no_useless_return' => true,
        
        
        // semicolon
        // Запретите многострочные пробелы перед закрывающей точкой с запятой или переместите точку с запятой на новую строку для связанных вызовов
        'multiline_whitespace_before_semicolons' => true,
        // Удалите ненужные утверждения (точки с запятой).
        'no_empty_statement' => true,
        // Пробелы перед ; запрещены
        'no_singleline_whitespace_before_semicolons' => true,
        // Точка с запятой после каждой инструкции
        'semicolon_after_instruction' => true,
        
        
        // strict
        // declare strict_types
        'declare_strict_types' => true,
        // Сравнение должно быть строгим
        'strict_comparison' => true,
        // Функции со строгим сравнением, in_array и тд
        'strict_param' => true,
        
        
        // string_notation
        // Преобразует неявные переменные в явные в строках в двойных кавычках или в синтаксисе heredoc
        'explicit_string_variable' => true,
        // Перед строками не должно быть двоичного флага
        'no_binary_string' => true,
        // Преобразует явные переменные в строках в двойных кавычках и синтаксисе heredoc из простого формата в сложный (${ в {$)
        'simple_to_complex_string_variable' => true,
        // Преобразование двойных кавычек в одинарные для простых строк
        'single_quote' => [
            'strings_containing_single_quote_chars' => true,
        ],
        // Строковые проверки на пустую строку должны выполняться против '', а не с помощью strlen
        'string_length_to_empty' => true,
        
        
        // whitespace
        // Каждый элемент массива должен иметь отступ ровно один раз
        'array_indentation' => true,
        // Пустой перевод строки должен предшествовать любому настроенному оператору
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'continue', 
                'declare', 
                'default', 
                'do', 
                'exit', 
                'for', 
                'foreach', 
                'goto', 
                'if', 
                'include', 
                'include_once',
                'require', 
                'require_once', 
                'return', 
                'switch', 
                'throw', 
                'try', 
                'while', 
                'yield', 
                'yield_from',
            ],
        ],
        // Размещение пустых строк между группами операторов использования
        'blank_line_between_import_groups' => true,
        // Удалите лишние пробелы в объявлении типа, допускающего значение null
        'compact_nullable_type_declaration' => true,
        // Цепочка методов ДОЛЖНА иметь правильный отступ. Цепочка методов с разными уровнями отступов не поддерживается
        'method_chaining_indentation' => true,
        // Удаляет лишние пустые строки и/или пустые строки после настройки
        'no_extra_blank_lines' => [
            'tokens' => [
                'attribute', 
                'break', 
                'case', 
                'continue', 
                'curly_brace_block', 
                'default', 
                'extra', 
                'parenthesis_brace_block', 
                'return', 
                'square_brace_block', 
                'switch', 
                'throw', 
                'use',
            ],
        ],
        // Вокруг смещенных скобок НЕ ДОЛЖНО быть пробелов
        'no_spaces_around_offset' => true,
        // Удалить пробелы в конце не пустых строк
        'no_trailing_whitespace' => true,
        // Удалите конечные пробелы в конце пустых строк
        'no_whitespace_in_blank_line' => true,
        // php-файл без закрывающего тега должен иметь перенос строки в конце
        'single_blank_line_at_eof' => true,
        // Круглые скобки должны быть объявлены с использованием настроенных пробелов
        'spaces_inside_parentheses' => true,
        //	Последний комментарий под if/else относится к текущему блоку, а не к следующему
        'statement_indentation' => [
            'stick_comment_to_next_continuous_control_statement' => false,
        ],
        // Обеспечьте одинарное пространство между переменной и объявлением ее типа в аргументах и свойствах функции
        'type_declaration_spaces' => true,
        // Вокруг операторов типа объединения и типа пересечения должен быть один пробел или ничего
        'types_spaces' => true,
    ]);
