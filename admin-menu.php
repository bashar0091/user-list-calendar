<?php

/**
 * Add New Admin Menu Registered
 */


function user_list_menu() {
    add_menu_page(
        'Registered User',
        'Registered User',
        'manage_options',
        'registered_user_list',
        'registered_user_page_content',
        'dashicons-admin-users',
        30
    );
}
add_action('admin_menu', 'user_list_menu');

// Callback function to display content
function registered_user_page_content() {
?>

    <div class="wrap">

        <div class="cols_2">
            <div>
                <div id="demoEvoCalendar"></div>

                <div style="margin-top: 50px">
                    <canvas id="user_chart"></canvas>
                </div>

                <?php
                    function count_users_in_month($year, $month) {
                        $args = array(
                            'date_query' => array(
                                array(
                                    'year' => $year,
                                    'month' => $month,
                                ),
                            ),
                        );
                        $users = get_users($args);
                        return count($users);
                    }

                    $user_counts = array();

                    for ($i = 1; $i <= 6; $i++) {
                        $user_counts[] = count_users_in_month(2024, $i);
                    }
                ?>
                <script>
                    const ctx = document.getElementById('user_chart');

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                        labels: ['January', 'February', 'March', 'April', 'May', 'June'],
                        datasets: [{
                            label: 'This Year',
                            data: <?php echo json_encode($user_counts); ?>,
                            borderWidth: 2
                        }]
                        },
                        options: {
                        scales: {
                            y: {
                            beginAtZero: true
                            }
                        }
                        }
                    });
                </script>

            </div>

            <div class="user_list_wrap">
                <span class="spinner"></span>
                <table class="wp-list-table widefat fixed striped user_list_table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php

                        $timezone = get_option( 'timezone_string' );
                        $current_date = new DateTime('now', new DateTimeZone($timezone));
                        
                        $args = array(
                            'date_query' => array(
                                array(
                                    'year' => $current_date->format('Y'),
                                    'month' => $current_date->format('n'),
                                    'day' => $current_date->format('j'),
                                ),
                            ),
                        );
                        $users = get_users($args);

                        if(!empty($users)) {
                            foreach ($users as $user) {
                                // Get user data
                                $username = $user->user_login;
                                $email = $user->user_email;
                                $registered_date = date('Y-m-d', strtotime($user->user_registered));
    
                                $first_name = get_user_meta($user->ID, 'first_name', true);
                                $last_name = get_user_meta($user->ID, 'last_name', true);
                                $billing_phone = get_user_meta($user->ID, 'billing_phone', true);
    
                                echo "<tr>
                                        <td>$username</td>
                                        <td>$first_name $last_name</td>
                                        <td>$email</td>
                                        <td>$billing_phone</td>
                                        <td>$registered_date</td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No User is found in this date</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

<?php
}