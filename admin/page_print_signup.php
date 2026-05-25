<?php

if (! defined('ABSPATH')) {
    exit;
}

function fcmanager_page_print_signup()
{
    $post_id = intval($_GET['post_id'] ?? 0);

    if (
        ! current_user_can('edit_post', $post_id)
        || ! wp_verify_nonce($_GET['_wpnonce'] ?? '', 'fcmanager_print_' . $post_id)
    ) {
        wp_die(__('Access denied.', 'football-club-manager'));
    }

    $signup = new FCManager_Signup($post_id);
?>
    <!DOCTYPE html>
    <html lang="nl">

    <head>
        <meta charset="UTF-8">
        <title><?php echo esc_html(get_the_title($signup->personal_details()->name())); ?></title>
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 13px;
                margin: 2cm;
            }

            h1 {
                font-size: 18px;
                text-align: center;
            }

            h2 {
                font-size: inherit;
                text-align: center;
                margin: 20px 0 0 0;
            }

            h3 {
                font-size: inherit;
            }

            .fcmanager_print_subtitle {
                text-align: center;
                font-style: italic;
                margin: 5px 0;
            }

            table {
                width: 100%;
                border-spacing: 10px 0;
                margin: 10px -16px;
            }

            table.fcmanager-vertical-table {
                border-collapse: collapse;
                margin-left: -6px;
                margin-right: -6px;
            }

            th,
            td {
                text-align: left;
                padding: 6px 8px;
                border-bottom: 1px solid #ccc;
            }

            th {
                width: 35%;
                color: #555;
                white-space: nowrap;
            }

            thead th {
                border-bottom: none;
                padding-bottom: 0;
            }

            .fcmanager-print-footer {
                color: #ccc;
                text-align: center;
            }

            @media print {
                body {
                    margin: 1cm;
                }
            }
        </style>
    </head>

    <body onload="window.print()">
        <h1><?php echo esc_html($signup->personal_details()->name()); ?></h1>
        <p class="fcmanager_print_subtitle"><?php echo esc_html($signup->type()); ?><?php echo $signup->subtype() ? ' - ' . esc_html($signup->subtype()) : ''; ?></em>
        <h2><?php echo esc_html__('Personal details', 'football-club-manager'); ?></h2>
        <?php
        if ($signup->personal_details()->initials() || $signup->personal_details()->first_name() || $signup->personal_details()->middle_name() || $signup->personal_details()->last_name()) {
        ?>
            <table>
                <thead>
                    <tr>
                        <th><?php echo esc_html__('Initials', 'football-club-manager'); ?></th>
                        <th><?php echo esc_html__('First name', 'football-club-manager'); ?></th>
                        <th><?php echo esc_html__('Middle name', 'football-club-manager'); ?></th>
                        <th><?php echo esc_html__('Last name', 'football-club-manager'); ?></th>
                    </tr>
                </thead>
                <tr>
                    <td><?php echo esc_html($signup->personal_details()->initials()); ?></td>
                    <td><?php echo esc_html($signup->personal_details()->first_name()); ?></td>
                    <td><?php echo esc_html($signup->personal_details()->middle_name()); ?></td>
                    <td><?php echo esc_html($signup->personal_details()->last_name()); ?></td>
                </tr>
            </table>
        <?php
        }

        $address_line1 = implode(' ', array_filter([$signup->personal_details()->street(), $signup->personal_details()->house_number() . $signup->personal_details()->house_number_addition()], 'strlen'));
        $address_line2 = implode(' ', array_filter([$signup->personal_details()->postal_code(), $signup->personal_details()->city()], 'strlen'));
        $address_line3 = $signup->personal_details()->country();
        if ($address_line1 || $address_line2 || $address_line3) {
        ?>
            <table>
                <thead>
                    <tr>
                        <th><?php echo esc_html__('Address', 'football-club-manager'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo esc_html($address_line1); ?></td>
                    </tr>
                    <?php if ($address_line2) { ?>
                        <tr>
                            <td><?php echo esc_html($address_line2); ?></td>
                        </tr>
                    <?php } ?>
                    <?php if ($address_line3) { ?>
                        <tr>
                            <td><?php echo esc_html($address_line3); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php
        }

        if ($signup->personal_details()->date_of_birth() || $signup->personal_details()->gender() || $signup->personal_details()->nationality()) {
        ?>
            <table>
                <thead>
                    <tr>
                        <?php if ($signup->personal_details()->date_of_birth()) { ?>
                            <th><?php echo esc_html__('Date of birth', 'football-club-manager'); ?></th>
                        <?php } ?>
                        <?php if ($signup->personal_details()->gender()) { ?>
                            <th><?php echo esc_html__('Gender', 'football-club-manager'); ?></th>
                        <?php } ?>
                        <?php if ($signup->personal_details()->nationality()) { ?>
                            <th><?php echo esc_html__('Nationality', 'football-club-manager'); ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php if ($signup->personal_details()->date_of_birth()) { ?>
                            <td><?php echo esc_html($signup->personal_details()->date_of_birth()->format('d-m-Y')); ?></td>
                        <?php } ?>
                        <?php if ($signup->personal_details()->gender()) { ?>
                            <td><?php echo esc_html__($signup->personal_details()->print_gender(), 'football-club-manager'); ?></td>
                        <?php } ?>
                        <?php if ($signup->personal_details()->nationality()) { ?>
                            <td><?php echo esc_html($signup->personal_details()->nationality()); ?></td>
                        <?php } ?>
                    </tr>
                </tbody>
            </table>
        <?php
        }

        if ($signup->personal_details()->mobile_phone_number() || $signup->personal_details()->phone_number() || $signup->personal_details()->email_address() || $signup->personal_details()->emergency_contact_number()) {
        ?>
            <table>
                <thead>
                    <tr>
                        <?php if ($signup->personal_details()->phone_number()) { ?>
                            <th><?php echo esc_html__('Phone number', 'football-club-manager'); ?></th>
                        <?php } ?>
                        <?php if ($signup->personal_details()->mobile_phone_number()) { ?>
                            <th><?php echo esc_html__('Mobile phone number', 'football-club-manager'); ?></th>
                        <?php } ?>
                        <?php if ($signup->personal_details()->email_address()) { ?>
                            <th><?php echo esc_html__('Email address', 'football-club-manager'); ?></th>
                        <?php } ?>
                        <?php if ($signup->personal_details()->emergency_contact_number()) { ?>
                            <th><?php echo esc_html__('Emergency contact number', 'football-club-manager'); ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php if ($signup->personal_details()->phone_number()) { ?>
                            <td><?php echo esc_html($signup->personal_details()->phone_number()); ?></td>
                        <?php } ?>
                        <?php if ($signup->personal_details()->mobile_phone_number()) { ?>
                            <td><?php echo esc_html($signup->personal_details()->mobile_phone_number()); ?></td>
                        <?php } ?>
                        <?php if ($signup->personal_details()->email_address()) { ?>
                            <td><?php echo esc_html($signup->personal_details()->email_address()); ?></td>
                        <?php } ?>
                        <?php if ($signup->personal_details()->emergency_contact_number()) { ?>
                            <td><?php echo esc_html($signup->personal_details()->emergency_contact_number()); ?></td>
                        <?php } ?>
                    </tr>
                </tbody>
            </table>
        <?php
        }

        $parent1set = $signup->parent1()->name() || $signup->parent1()->phone_number() || $signup->parent1()->email_address();
        $parent2set = $signup->parent2()->name() || $signup->parent2()->phone_number() || $signup->parent2()->email_address();

        if ($parent1set || $parent2set) {
        ?>
            <h2><?php echo esc_html__('Parent details', 'football-club-manager'); ?></h2>
            <?php if ($parent1set) { ?>
                <h3><?php echo esc_html__('Parent/guardian 1', 'football-club-manager'); ?></h3>
                <?php if ($signup->parent1()->name()) { ?>
                    <table>
                        <thead>
                            <tr>
                                <th><?php echo esc_html__('First name', 'football-club-manager'); ?></th>
                                <th><?php echo esc_html__('Middle name', 'football-club-manager'); ?></th>
                                <th><?php echo esc_html__('Last name', 'football-club-manager'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo esc_html($signup->parent1()->first_name()); ?></td>
                                <td><?php echo esc_html($signup->parent1()->middle_name()); ?></td>
                                <td><?php echo esc_html($signup->parent1()->last_name()); ?></td>
                            </tr>
                        </tbody>
                    </table>
                <?php } ?>
                <?php if ($signup->parent1()->phone_number() || $signup->parent1()->mobile_phone_number() || $signup->parent1()->email_address()) { ?>
                    <table>
                        <thead>
                            <tr>
                                <th><?php echo esc_html__('Phone number', 'football-club-manager'); ?></th>
                                <th><?php echo esc_html__('Mobile phone number', 'football-club-manager'); ?></th>
                                <th><?php echo esc_html__('Email address', 'football-club-manager'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo esc_html($signup->parent1()->phone_number()); ?></td>
                                <td><?php echo esc_html($signup->parent1()->mobile_phone_number()); ?></td>
                                <td><?php echo esc_html($signup->parent1()->email_address()); ?></td>
                            </tr>
                        </tbody>
                    </table>
                <?php } ?>
            <?php } ?>

            <?php if ($parent2set) { ?>
                <h3><?php echo esc_html__('Parent/guardian 2', 'football-club-manager'); ?></h3>
                <?php if ($signup->parent2()->name()) { ?>
                    <table>
                        <thead>
                            <tr>
                                <th><?php echo esc_html__('First name', 'football-club-manager'); ?></th>
                                <th><?php echo esc_html__('Middle name', 'football-club-manager'); ?></th>
                                <th><?php echo esc_html__('Last name', 'football-club-manager'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo esc_html($signup->parent2()->first_name()); ?></td>
                                <td><?php echo esc_html($signup->parent2()->middle_name()); ?></td>
                                <td><?php echo esc_html($signup->parent2()->last_name()); ?></td>
                            </tr>
                        </tbody>
                    </table>
                <?php } ?>
                <?php if ($signup->parent2()->phone_number() || $signup->parent2()->mobile_phone_number() || $signup->parent2()->email_address()) { ?>
                    <table>
                        <thead>
                            <tr>
                                <th><?php echo esc_html__('Phone number', 'football-club-manager'); ?></th>
                                <th><?php echo esc_html__('Mobile phone number', 'football-club-manager'); ?></th>
                                <th><?php echo esc_html__('Email address', 'football-club-manager'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo esc_html($signup->parent2()->phone_number()); ?></td>
                                <td><?php echo esc_html($signup->parent2()->mobile_phone_number()); ?></td>
                                <td><?php echo esc_html($signup->parent2()->email_address()); ?></td>
                            </tr>
                        </tbody>
                    </table>
                <?php } ?>
            <?php } ?>
        <?php
        }

        if ($signup->payment_details()->method()) {
        ?>
            <h2><?php echo esc_html__('Payment details', 'football-club-manager'); ?></h2>
            <table>
                <thead>
                    <tr>
                        <?php if ($signup->payment_details()->iban()) { ?>
                            <th><?php echo esc_html__('IBAN', 'football-club-manager'); ?></th>
                        <?php } ?>
                        <?php if ($signup->payment_details()->account_holder_name()) { ?>
                            <th><?php echo esc_html__('Account holder name', 'football-club-manager'); ?></th>
                        <?php } ?>
                        <?php if ($signup->payment_details()->reason()) { ?>
                            <th><?php echo esc_html__('No payment needed', 'football-club-manager'); ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php if ($signup->payment_details()->iban()) { ?>
                            <td><?php echo esc_html($signup->payment_details()->iban()); ?></td>
                        <?php } ?>
                        <?php if ($signup->payment_details()->account_holder_name()) { ?>
                            <td><?php echo esc_html($signup->payment_details()->account_holder_name()); ?></td>
                        <?php } ?>
                        <?php if ($signup->payment_details()->reason()) { ?>
                            <td><?php echo esc_html($signup->payment_details()->reason()); ?></td>
                        <?php } ?>
                    </tr>
                </tbody>
            </table>
        <?php
        }

        $additional = $signup->additional_information()->getAll();
        if (!empty($additional)) {
        ?>
            <h2><?php echo esc_html__('Additional information', 'football-club-manager'); ?></h2>
            <table class="fcmanager-vertical-table">
                <?php foreach ($additional as $key => $value) : ?>
                    <tr>
                        <th><?php echo esc_html($key); ?></th>
                        <td><?php echo esc_html($value); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php
        }

        ?>
        <p class="fcmanager-print-footer">
            <?php
            /* translators: %s: Current date and time */
            echo esc_html(sprintf(__('Printed on %s', 'football-club-manager'), date('d-m-Y H:i')));
            ?>
        </p>
    </body>

    </html>
<?php
}
