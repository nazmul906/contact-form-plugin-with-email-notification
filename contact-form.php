<?php
/*
Plugin Name: Contact Form
Description: A plugin to add contact form
Author Name: Nazmul
Version: 1.00
*/

if (!function_exists('add_shortcode')) {
    echo "You are in the wrong tunnel";
    exit;
}

function add_contact_form()
{
    $form = '
        <form id="contactForm" method="post">
            <label for="title">Post Title:</label>
            <input type="text" name="title" id="title" required> <br>

            <label for="content">Post Content:</label>
            <input type="text" name="content" id="content" required> <br>

            <label for="name">Your Name:</label>
            <input type="text" name="name" id="name" required> <br>

            <label for="email">Your Email:</label>
            <input type="email" name="email" id="email" required><br>

            <button type="button" id="submit">Submit</button>

        </form>

    

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const submitButton = document.getElementById("submit");
              

                submitButton.addEventListener("click", function() {
                    var title = document.getElementById("title").value;
                    var content = document.getElementById("content").value;
                    var name = document.getElementById("name").value;
                    var email = document.getElementById("email").value;

                    var formData = new FormData();
                    formData.append("action", "process_contact_form");
                    formData.append("post_title", title);
                    formData.append("post_content", content);
                    formData.append("your_name", name);
                    formData.append("email", email);
                    
                    console.log("Form Data:");

                    for (var i of formData.entries()) {
                        console.log(i[0] + ": " + i[1]);
                    }

                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "'.admin_url('admin-ajax.php').'");
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === XMLHttpRequest.DONE) {
                            if (xhr.status === 200) {
                                console.log("req is recieved");
                            } else {
                                console.log("there is an error");
                            }
                        }
                    };
                    xhr.send(formData);
                });
            });
        </script>
    ';

    return $form;
}

add_shortcode('contact_form', 'add_contact_form');

function process_contact()
{

    wp_die(); 
}

add_action('wp_ajax_process_contact_form', 'process_contact');
add_action('wp_ajax_nopriv_process_contact_form', 'process_contact');