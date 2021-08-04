<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    {{-- <link rel="stylesheet" type="text/css" href="{{ url('/css/email.css') }}"> --}}
    <title>{{ $generalSettings['site_name'] }}</title>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
    <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
        <tr>
            <td align="center" valign="top" id="bodyCell">
                <!-- BEGIN TEMPLATE // -->
                <table border="0" cellpadding="0" cellspacing="0" id="templateContainer">
                    <tr>
                        <td align="center" valign="top">
                            <!-- BEGIN PREHEADER // -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templatePreheader">
                                <tr>
                                    <td class="headerContent" width="100%">
                                        <a href=""><img src="{{ url($generalSettings['logo']) }}" id="headerImage campaign-icon" mc:label="header_image" mc:edit="header_image" mc:allowtext/></a>
                                    </td>
                                </tr>
                            </table>
                            <!-- // END PREHEADER -->
                        </td>
                    </tr>
                    <tr>
                        <td align="center" valign="top">
                            <!-- BEGIN BODY // -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">
                                <tr>
                                    @yield('body')

                                </tr>
                            </table>
                            <!-- // END BODY -->
                        </td>
                    </tr>
                    <tr>
                        <td align="center" valign="top">
                            <!-- BEGIN FOOTER // -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateFooter">
                                <tr>
                                    <td valign="top" class="footerContent" mc:edit="footer_content02">
                                        @yield('cancel')
                                    </td>
                                </tr>
                                {{--   <tr>
                                       <td valign="top" class="footerContent" mc:edit="footer_content00">
                                           <a href="*|TWITTER:PROFILEURL|*">Follow on Twitter</a>&nbsp;&nbsp;&nbsp;<a href="*|FACEBOOK:PROFILEURL|*">Friend on Facebook</a>&nbsp;&nbsp;&nbsp;<a href="*|FORWARD|*">Forward to Friend</a>&nbsp;
                                       </td>
                                   </tr>--}}
                            </table>
                            <!-- // END FOOTER -->
                        </td>
                    </tr>
                </table>
                <!-- // END TEMPLATE -->
            </td>
        </tr>
    </table>
</body>
</html>
