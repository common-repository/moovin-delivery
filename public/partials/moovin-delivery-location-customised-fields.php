<?php

$fields[$address_type_section . '_door']   = array(
    'label'        => esc_html('Door / Flat No.'),
    'required'     => false,
    'type' => 'text',
    'class'        => array('input-text sg_del_add_hidden_fields'),
    'priority'     => 100,
);

$fields[$address_type_section . '_landmark']   = array(
    'label'        => esc_html('Landmark'),
    'required'     => false,
    'type' => 'text',
    'class'        => array('input-text sg_del_add_hidden_fields'),
    'priority'     => 101,
);

$fields[$address_type_section . '_address_type']   = array(
    'label'        => esc_html('address type'),
    'required'     => false,
    'type' => 'text',
    'class' => array('input-text sg_del_add_hidden_fields'),
    'priority'     => 102,
);

$fields[$address_type_section . '_address_latitude']   = array(
    'label'        => esc_html('address latitude'),
    'required'     => false,
    'type' => 'text',
    'class' => array('input-text sg_del_add_hidden_fields'),
    'priority'     => 103,
);

$fields[$address_type_section . '_address_longitude']   = array(
    'label'        => esc_html('address longitude'),
    'required'     => false,
    'type' => 'text',
    'class' => array('input-text sg_del_add_hidden_fields'),
    'priority'     => 104,
);
