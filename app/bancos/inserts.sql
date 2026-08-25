INSERT INTO
    `corte` (
        `corte_nome`,
        `corte_preco`,
        `corte_descricao`
    )
VALUES (
        'Corte Social',
        24.99,
        'O corte ideal para você!'
    ),
    (
        'Navalhado',
        29.99,
        'Cabelo na régua total.'
    ),
    (
        'Barba',
        19.99,
        'Barba feita com perfeição.'
    ),
    (
        'Sobrancelha',
        9.99,
        'Definição precisa.'
    ),
    (
        'Completo',
        59.99,
        'Corte + barba+ sobrancelha.'
    ),
    (
        'Pintar',
        29.99,
        'Pintura capilar profissional.'
    );

INSERT INTO
    dia_inativo (
        diaInativo_data_inativa,
        diaInativo_hora_inicio,
        diaInativo_hora_fim,
        diaInativo_motivo
    )
VALUES (
        '2025-10-31',
        '10:30:00',
        '11:00:00',
        'Ja marcado'
    ),
    (
        '2025-11-07',
        '15:30:00',
        '16:00:00',
        'Ja marcado'
    );

INSERT INTO
    estoque (est_nome, est_qtd)
VALUES ('Maquina de barbear', 4),
    ('Tesoura', 6),
    ('Creme gel', 2);

INSERT INTO
    `agendamento` (
        `agen_id`,
        `agen_data_a`,
        `agen_hora_a`,
        `agen_data_c`,
        `usuario_id`,
        `corte_id`,
        `agen_status`
    )
VALUES (
        0,
        '2025-09-30',
        '12:00:00',
        '2025-10-21 11:39:00',
        9,
        3,
        'Confirmado'
    ),
    (
        2,
        '2025-10-11',
        '12:00:00',
        '2025-10-07 12:38:29',
        9,
        3,
        'Confirmado'
    ),
    (
        3,
        '2025-10-10',
        '12:00:00',
        '2025-10-07 13:17:40',
        11,
        3,
        'Confirmado'
    );

INSERT INTO
    `corte` (
        `corte_id`,
        `corte_nome`,
        `corte_preco`,
        `corte_descricao`
    )
VALUES (
        2,
        'Corte Social',
        24.99,
        'O corte ideal para você!'
    ),
    (
        3,
        'Navalhado',
        29.99,
        'Cabelo na régua total.'
    ),
    (
        4,
        'Barba',
        19.99,
        'Barba feita com perfeição.'
    ),
    (
        5,
        'Sobrancelha',
        9.99,
        'Definição precisa.'
    ),
    (
        6,
        'Completo',
        59.99,
        'Corte + barba+ sobrancelha.'
    ),
    (
        7,
        'Pintar',
        29.99,
        'Pintura capilar profissional.'
    );