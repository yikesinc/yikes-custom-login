<?php
/**
 * Password Reset Email Template
 * @since 1.0
 */

// If accessed directly, abort
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="background:#f3f3f3; min-height:100%">

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
        <style>@media only screen and (max-width: 596px) {
    .small-float-center {
        margin: 0 auto;
        float: none;
        text-align: center
        }
    .small-text-center {
        text-align: center
        }
    .small-text-left {
        text-align: left
        }
    .small-text-right {
        text-align: right
        }
    }
@media only screen and (max-width: 596px) {
    table.body table.container .hide-for-large {
        display: block;
        width: auto;
        overflow: visible
        }
    }
@media only screen and (max-width: 596px) {
    table.body table.container .row.hide-for-large, table.body table.container .row.hide-for-large {
        display: table;
        width: 100%
        }
    }
@media only screen and (max-width: 596px) {
    table.body table.container .show-for-large {
        display: none;
        width: 0;
        mso-hide: all;
        overflow: hidden
        }
    }
a:hover {color:#147dc2}
a:active {color:#147dc2}
a:visited {color:#2199e8}
h1 a:visited {color:#2199e8}
h2 a:visited {color:#2199e8}
h3 a:visited {color:#2199e8}
h4 a:visited {color:#2199e8}
h5 a:visited {color:#2199e8}
h6 a:visited {color:#2199e8}
table.button:hover table tr td a {color:#fefefe}
table.button:active table tr td a {color:#fefefe}
table.button table tr td a:visited {color:#fefefe}
table.button.tiny:hover table tr td a {color:#fefefe}
table.button.tiny:active table tr td a {color:#fefefe}
table.button.tiny table tr td a:visited {color:#fefefe}
table.button.small:hover table tr td a {color:#fefefe}
table.button.small:active table tr td a {color:#fefefe}
table.button.small table tr td a:visited {color:#fefefe}
table.button.large:hover table tr td a {color:#fefefe}
table.button.large:active table tr td a {color:#fefefe}
table.button.large table tr td a:visited {color:#fefefe}
table.button:hover table td {background:#147dc2;color:#fefefe}
table.button:visited table td {background:#147dc2;color:#fefefe}
table.button:active table td {background:#147dc2;color:#fefefe}
table.button:hover table a {border:0 solid #147dc2}
table.button:visited table a {border:0 solid #147dc2}
table.button:active table a {border:0 solid #147dc2}
table.button.secondary:hover table td {background:#919191;color:#fefefe}
table.button.secondary:hover table a {border:0 solid #919191}
table.button.secondary:hover table td a {color:#fefefe}
table.button.secondary:active table td a {color:#fefefe}
table.button.secondary table td a:visited {color:#fefefe}
table.button.success:hover table td {background:#23bf5d}
table.button.success:hover table a {border:0 solid #23bf5d}
table.button.alert:hover table td {background:#e23317}
table.button.alert:hover table a {border:0 solid #e23317}
.thumbnail:hover {box-shadow:0 0 6px 1px rgba(33, 153, 232, 0.5)}
.thumbnail:focus {box-shadow:0 0 6px 1px rgba(33, 153, 232, 0.5)}
@media only screen and (max-width: 596px) {
    table.body img {
        width: auto;
        height: auto
        }
    table.body center {
        min-width: 0
        }
    table.body .container {
        width: 95%
        }
    table.body .columns, table.body .column {
        height: auto;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        padding-left: 16px;
        padding-right: 16px
        }
    table.body .columns .column, table.body .columns .columns, table.body .column .column, table.body .column .columns {
        padding-left: 0;
        padding-right: 0
        }
    table.body .collapse .columns, table.body .collapse .column {
        padding-left: 0;
        padding-right: 0
        }
    td.small-1, th.small-1 {
        display: inline-block;
        width: 8.33333%
        }
    td.small-2, th.small-2 {
        display: inline-block;
        width: 16.66667%
        }
    td.small-3, th.small-3 {
        display: inline-block;
        width: 25%
        }
    td.small-4, th.small-4 {
        display: inline-block;
        width: 33.33333%
        }
    td.small-5, th.small-5 {
        display: inline-block;
        width: 41.66667%
        }
    td.small-6, th.small-6 {
        display: inline-block;
        width: 50%
        }
    td.small-7, th.small-7 {
        display: inline-block;
        width: 58.33333%
        }
    td.small-8, th.small-8 {
        display: inline-block;
        width: 66.66667%
        }
    td.small-9, th.small-9 {
        display: inline-block;
        width: 75%
        }
    td.small-10, th.small-10 {
        display: inline-block;
        width: 83.33333%
        }
    td.small-11, th.small-11 {
        display: inline-block;
        width: 91.66667%
        }
    td.small-12, th.small-12 {
        display: inline-block;
        width: 100%
        }
    .columns td.small-12, .column td.small-12, .columns th.small-12, .column th.small-12 {
        display: block;
        width: 100%
        }
    .body .columns td.small-1, .body .column td.small-1, td.small-1 center, .body .columns th.small-1, .body .column th.small-1, th.small-1 center {
        display: inline-block;
        width: 8.33333%
        }
    .body .columns td.small-2, .body .column td.small-2, td.small-2 center, .body .columns th.small-2, .body .column th.small-2, th.small-2 center {
        display: inline-block;
        width: 16.66667%
        }
    .body .columns td.small-3, .body .column td.small-3, td.small-3 center, .body .columns th.small-3, .body .column th.small-3, th.small-3 center {
        display: inline-block;
        width: 25%
        }
    .body .columns td.small-4, .body .column td.small-4, td.small-4 center, .body .columns th.small-4, .body .column th.small-4, th.small-4 center {
        display: inline-block;
        width: 33.33333%
        }
    .body .columns td.small-5, .body .column td.small-5, td.small-5 center, .body .columns th.small-5, .body .column th.small-5, th.small-5 center {
        display: inline-block;
        width: 41.66667%
        }
    .body .columns td.small-6, .body .column td.small-6, td.small-6 center, .body .columns th.small-6, .body .column th.small-6, th.small-6 center {
        display: inline-block;
        width: 50%
        }
    .body .columns td.small-7, .body .column td.small-7, td.small-7 center, .body .columns th.small-7, .body .column th.small-7, th.small-7 center {
        display: inline-block;
        width: 58.33333%
        }
    .body .columns td.small-8, .body .column td.small-8, td.small-8 center, .body .columns th.small-8, .body .column th.small-8, th.small-8 center {
        display: inline-block;
        width: 66.66667%
        }
    .body .columns td.small-9, .body .column td.small-9, td.small-9 center, .body .columns th.small-9, .body .column th.small-9, th.small-9 center {
        display: inline-block;
        width: 75%
        }
    .body .columns td.small-10, .body .column td.small-10, td.small-10 center, .body .columns th.small-10, .body .column th.small-10, th.small-10 center {
        display: inline-block;
        width: 83.33333%
        }
    .body .columns td.small-11, .body .column td.small-11, td.small-11 center, .body .columns th.small-11, .body .column th.small-11, th.small-11 center {
        display: inline-block;
        width: 91.66667%
        }
    table.body td.small-offset-1, table.body th.small-offset-1 {
        margin-left: 8.33333%;
        margin-left: 8.33333%
        }
    table.body td.small-offset-2, table.body th.small-offset-2 {
        margin-left: 16.66667%;
        margin-left: 16.66667%
        }
    table.body td.small-offset-3, table.body th.small-offset-3 {
        margin-left: 25%;
        margin-left: 25%
        }
    table.body td.small-offset-4, table.body th.small-offset-4 {
        margin-left: 33.33333%;
        margin-left: 33.33333%
        }
    table.body td.small-offset-5, table.body th.small-offset-5 {
        margin-left: 41.66667%;
        margin-left: 41.66667%
        }
    table.body td.small-offset-6, table.body th.small-offset-6 {
        margin-left: 50%;
        margin-left: 50%
        }
    table.body td.small-offset-7, table.body th.small-offset-7 {
        margin-left: 58.33333%;
        margin-left: 58.33333%
        }
    table.body td.small-offset-8, table.body th.small-offset-8 {
        margin-left: 66.66667%;
        margin-left: 66.66667%
        }
    table.body td.small-offset-9, table.body th.small-offset-9 {
        margin-left: 75%;
        margin-left: 75%
        }
    table.body td.small-offset-10, table.body th.small-offset-10 {
        margin-left: 83.33333%;
        margin-left: 83.33333%
        }
    table.body td.small-offset-11, table.body th.small-offset-11 {
        margin-left: 91.66667%;
        margin-left: 91.66667%
        }
    table.body table.columns td.expander, table.body table.columns th.expander {
        display: none
        }
    table.body .right-text-pad, table.body .text-pad-right {
        padding-left: 10px
        }
    table.body .left-text-pad, table.body .text-pad-left {
        padding-right: 10px
        }
    table.menu {
        width: 100%
        }
    table.menu td, table.menu th {
        width: auto;
        display: inline-block
        }
    table.menu.vertical td, table.menu.vertical th, table.menu.small-vertical td, table.menu.small-vertical th {
        display: block
        }
    table.button.expand {
        width: 100%
        }
    }</style>

    </head>

  <body style="-moz-box-sizing:border-box; -ms-text-size-adjust:100%; -webkit-box-sizing:border-box; -webkit-text-size-adjust:100%; box-sizing:border-box; margin:0; min-width:100%; padding:0; color:#0a0a0a; font-family:Helvetica, Arial, sans-serif; font-weight:normal; line-height:22px; text-align:left; font-size:18px; width:100%" align="left" width="100%">

    <table data-made-with-foundation="" style="border-collapse:collapse; border-spacing:0; padding:0; text-align:left; vertical-align:top; background:#f3f3f3; height:100%; width:100%; color:#0a0a0a; font-family:Helvetica, Arial, sans-serif; font-weight:normal; line-height:22px; margin:0; font-size:18px" align="left" valign="top" height="100%" width="100%">
      <tr style="padding:0; text-align:left; vertical-align:top" align="left" valign="top">
        <td align="left" valign="top" style="-moz-hyphens:auto; -webkit-hyphens:auto; hyphens:auto; word-wrap:break-word; padding:0; text-align:left; vertical-align:top; color:#0a0a0a; font-family:Helvetica, Arial, sans-serif; font-weight:normal; line-height:22px; margin:0; font-size:18px; border-collapse:collapse">
          <center data-parsed="" style="min-width:580px; width:100%" width="100%">
            <table style="border-collapse:collapse; border-spacing:0; padding:0; text-align:center; vertical-align:top; background:#fefefe; margin:0 auto; width:580px" align="center" valign="top" width="580">
              <tbody>
                <tr style="padding:0; text-align:left; vertical-align:top" align="left" valign="top">
                  <td style="-moz-hyphens:auto; -webkit-hyphens:auto; hyphens:auto; word-wrap:break-word; padding:0; text-align:left; vertical-align:top; color:#0a0a0a; font-family:Helvetica, Arial, sans-serif; font-weight:normal; line-height:22px; margin:0; font-size:18px; border-collapse:collapse" align="left" valign="top">
                    <table style="border-collapse:collapse; border-spacing:0; padding:0; text-align:left; vertical-align:top; position:relative; width:100%; display:table" align="left" valign="top" width="100%">
                      <tbody>
                        <tr style="padding:0; text-align:left; vertical-align:top" align="left" valign="top">
                          <th style="color:#0a0a0a; font-family:Helvetica, Arial, sans-serif; font-weight:normal; line-height:22px; margin:0 auto; padding:0; text-align:left; font-size:18px; padding-bottom:16px; padding-left:16px; padding-right:16px; width:564px" align="left" width="564">
                            <table style="border-collapse:collapse; border-spacing:0; padding:0; text-align:left; vertical-align:top; width:100%" align="left" valign="top" width="100%">
                              <tr style="padding:0; text-align:left; vertical-align:top" align="left" valign="top">
                                <th style="color:#0a0a0a; font-family:Helvetica, Arial, sans-serif; font-weight:normal; line-height:22px; margin:0; padding:0; text-align:left; font-size:18px" align="left">

																	<!-- Site Logo, if set -->
																	<?php if( isset( $this->options['branding_logo'] ) && ! empty( $this->options['branding_logo'] ) ) { ?>
	                                  <center data-parsed="" style="min-width:532px; width:100%" width="100%">
																			<img src="<?php echo esc_url( $this->options['branding_logo'] ); ?>" align="center" style="-ms-interpolation-mode:bicubic; clear:both; display:block; max-width:100%; outline:none; text-decoration:none; width:auto; float:none; margin:0 auto; text-align:center; margin-bottom: 1em; margin-top: 1em;" width="auto">
																		</center>
																	<?php } ?>
																	<!-- End Site Logo -->


																	<!-- Site title -->
																	<h1 style="color:inherit; font-family:Helvetica, Arial, sans-serif; font-weight:normal; line-height:1.3; margin:0; padding:0; text-align:center; margin-bottom:15px; word-wrap:normal; font-size:30px" align="center">
																		<?php esc_attr_e( 'Forgot Your Password?', 'custom-wp-login' ); ?>
																	</h1>
																	<!-- End Site title -->

																	<p style="margin:0; color:#0a0a0a; font-family:Helvetica, Arial, sans-serif; font-weight:normal; line-height:22px; padding:0; text-align:center; font-size:18px; margin-bottom:10px" align="center">
																		<em><?php esc_attr_e( "Don't worry, it happens!", 'custom-wp-login' ); ?></em>
																	</p>

																	<p style="margin:0; color:#0a0a0a; font-family:Helvetica, Arial, sans-serif; font-weight:normal; line-height:22px; padding:0; text-align:center; font-size:18px; margin-bottom:10px" align="center">
																		<?php esc_attr_e( "If this was a mistake, or you didn't ask for a password reset, please disregard this email. If you did request a password reset - please click the link below.", 'custom-wp-login' ); ?>
																	</p>

                                  <table style="border-collapse:collapse; border-spacing:0; padding:0; text-align:left; vertical-align:top; margin:15px 0; width:100%" align="left" valign="top" width="100%">
                                    <tr style="padding:0; text-align:left; vertical-align:top" align="left" valign="top">
                                      <td style="-moz-hyphens:auto; -webkit-hyphens:auto; hyphens:auto; word-wrap:break-word; padding:0; text-align:left; vertical-align:top; color:#0a0a0a; font-family:Helvetica, Arial, sans-serif; font-weight:normal; line-height:22px; margin:0; font-size:18px; border-collapse:collapse" align="left" valign="top">
                                        <table style="border-collapse:collapse; border-spacing:0; padding:0; text-align:left; vertical-align:top; width:100%; margin-top:1em;" align="left" valign="top" width="100%">
                                          <tr style="padding:0; text-align:left; vertical-align:top" align="left" valign="top">
                                            <td style="-moz-hyphens:auto; -webkit-hyphens:auto; hyphens:auto; word-wrap:break-word; padding:0; text-align:left; vertical-align:top; color:#fefefe; font-family:Helvetica, Arial, sans-serif; font-weight:normal; line-height:22px; margin:0; font-size:18px; background:#2199e8; border:2px solid #2199e8; border-collapse:collapse; width:auto" align="left" valign="top" width="auto">
                                              <center data-parsed="" style="min-width:0; width:100%" width="100%">
																								<a href="<?php echo esc_url( $reset_pass_url ); ?>" align="center" style="color:#fefefe; font-family:Helvetica, Arial, sans-serif; font-weight:bold; line-height:1.3; margin:0; padding:10px 20px 10px 20px; text-align:center; text-decoration:none; width:calc(100% - 20px); border:0 solid #2199e8; border-radius:3px; display:inline-block; font-size:20px" width="calc(100% - 20px)">
																									<?php esc_attr_e( 'Reset Password', 'custom-wp-login' ); ?>
																								</a>
																							</center>
                                            </td>
                                            <td style="-moz-hyphens:auto; -webkit-hyphens:auto; hyphens:auto; word-wrap:break-word; padding:0; text-align:left; vertical-align:top; color:#fefefe; font-family:Helvetica, Arial, sans-serif; font-weight:normal; line-height:22px; margin:0; font-size:18px; visibility:hidden; width:auto; background:#2199e8; border:2px solid #2199e8; border-collapse:collapse" align="left" valign="top" width="auto"></td>
                                          </tr>
                                        </table>
                                      </td>
                                    </tr>
                                  </table>

                                  <hr style="border-bottom:1px solid #cacaca; border-left:0; border-right:0; border-top:0; clear:both; height:0; margin:20px auto; max-width:580px" height="0">

																	<p style="margin:0; color:#0a0a0a; font-family:Helvetica, Arial, sans-serif; font-weight:normal; line-height:22px; padding:0; text-align:left; font-size:18px; margin-bottom:10px" align="left">
																		<small style="color:#cacaca; font-size:80%">
																			<?php printf( esc_html_x( "You're getting this email because you requested to reset your password at %s.", '<a> tag, linking back to the site to reset the password.', 'custom-wp-login' ), '<a href="' . get_bloginfo( 'url' ) . '">' . get_bloginfo( 'name' ) . '</a>' ); ?>
																		</small>
																	</p>

                                </th>
                                <th style="color:#0a0a0a; font-family:Helvetica, Arial, sans-serif; font-weight:normal; line-height:22px; margin:0; padding:0; text-align:left; font-size:18px; visibility:hidden; width:0" align="left" width="0"></th>
                              </tr>
                            </table>
                          </th>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </center>
        </td>
      </tr>
    </table>
  </body>

</html>
