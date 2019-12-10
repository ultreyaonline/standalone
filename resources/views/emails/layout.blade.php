{{-- NOTE: sections include:  title, teaser_text, view_in_browser__url, view_in_browser__text, headline_text, content --}}
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <!-- NAME: SOPHISTICATED -->
  <!--[if gte mso 15]>
  <xml>
    <o:OfficeDocumentSettings>
      <o:AllowPNG/>
      <o:PixelsPerInch>96</o:PixelsPerInch>
    </o:OfficeDocumentSettings>
  </xml>
  <![endif]-->
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title')</title>

  <style type="text/css">
    p{
      margin:10px 0;
      padding:0;
    }
    table{
      border-collapse:collapse;
    }
    h1,h2,h3,h4,h5,h6{
      display:block;
      margin:0;
      padding:0;
    }
    img,a img{
      border:0;
      height:auto;
      outline:none;
      text-decoration:none;
    }
    body,#bodyTable,#bodyCell{
      height:100%;
      margin:0;
      padding:0;
      width:100%;
    }
    #outlook a{
      padding:0;
    }
    img{
      -ms-interpolation-mode:bicubic;
    }
    table{
      mso-table-lspace:0pt;
      mso-table-rspace:0pt;
    }
    .ReadMsgBody{
      width:100%;
    }
    .ExternalClass{
      width:100%;
    }
    p,a,li,td,blockquote{
      mso-line-height-rule:exactly;
    }
    a[href^=tel],a[href^=sms]{
      color:inherit;
      cursor:default;
      text-decoration:none;
    }
    p,a,li,td,body,table,blockquote{
      -ms-text-size-adjust:100%;
      -webkit-text-size-adjust:100%;
    }
    .ExternalClass,.ExternalClass p,.ExternalClass td,.ExternalClass div,.ExternalClass span,.ExternalClass font{
      line-height:100%;
    }
    a[x-apple-data-detectors]{
      color:inherit !important;
      text-decoration:none !important;
      font-size:inherit !important;
      font-family:inherit !important;
      font-weight:inherit !important;
      line-height:inherit !important;
    }
    a.mcnButton{
      display:block;
    }
    .mcnImage{
      vertical-align:bottom;
    }
    .mcnTextContent{
      word-break:break-word;
    }
    .mcnTextContent img{
      height:auto !important;
    }
    .mcnDividerBlock{
      table-layout:fixed !important;
    }
    body,#bodyTable{
      background-color:#ffffff;
    }
    #bodyCell{
      border-top:0;
    }
    #templateContainer{
      border:0;
    }
    h1{
      color:#F9F8F7 !important;
      font-family:Helvetica;
      font-size:50px;
      font-style:normal;
      font-weight:bold;
      line-height:125%;
      letter-spacing:-2px;
      text-align:left;
    }
    h2{
      color:#737475 !important;
      font-family:Helvetica;
      font-size:26px;
      font-style:normal;
      font-weight:bold;
      line-height:125%;
      letter-spacing:normal;
      text-align:left;
    }
    h3{
      color:#E7B932 !important;
      font-family:Helvetica;
      font-size:18px;
      font-style:normal;
      font-weight:bold;
      line-height:125%;
      letter-spacing:normal;
      text-align:left;
    }
    h4{
      color:#808080 !important;
      font-family:Helvetica;
      font-size:16px;
      font-style:normal;
      font-weight:bold;
      line-height:125%;
      letter-spacing:normal;
      text-align:left;
    }
    #templatePreheader{
      background-color:#ffffff;
      border-top:0;
      border-bottom:0;
    }
    .preheaderContainer .mcnTextContent,.preheaderContainer .mcnTextContent p{
      color:#a5a2b3;
      font-family:Helvetica;
      font-size:12px;
      line-height:125%;
      text-align:left;
    }
    .preheaderContainer .mcnTextContent a{
      color:#eba124;
      font-weight:normal;
      text-decoration:none;
    }
    #templateHeader{
      background-color:#ffffff;
      border-top:0;
      border-bottom:0;
    }
    .headerContainer .mcnTextContent,.headerContainer .mcnTextContent p{
      color:#e0e2e6;
      font-family:Helvetica;
      font-size:36px;
      line-height:125%;
      text-align:left;
    }
    .headerContainer .mcnTextContent a{
      color:#d89422;
      font-weight:normal;
      text-decoration:none;
    }
    #templateBody{
      background-color:#ffffff;
      border-top:0;
      border-bottom:0;
    }
    .bodyContainer .mcnTextContent,.bodyContainer .mcnTextContent p{
      color:#3d3a4c;
      font-family:Helvetica;
      font-size:16px;
      line-height:150%;
      text-align:left;
    }
    .bodyContainer .mcnTextContent a{
      color:#0090db;
      font-weight:normal;
      text-decoration:none;
    }
    #templateColumns{
      background-color:#ffffff;
      border-top:0;
      border-bottom:0;
    }
    .leftColumnContainer .mcnTextContent,.leftColumnContainer .mcnTextContent p{
      color:#3d3a4c;
      font-family:Helvetica;
      font-size:14px;
      line-height:150%;
      text-align:left;
    }
    .leftColumnContainer .mcnTextContent a{
      color:#0090db;
      font-weight:normal;
      text-decoration:none;
    }
    .rightColumnContainer .mcnTextContent,.rightColumnContainer .mcnTextContent p{
      color:#737475;
      font-family:Helvetica;
      font-size:14px;
      line-height:150%;
      text-align:left;
    }
    .rightColumnContainer .mcnTextContent a{
      color:#0090db;
      font-weight:normal;
      text-decoration:none;
    }
    #templateFooter{
      background-color:#ffffff;
      border-top:0;
      border-bottom:0;
    }
    .footerContainer .mcnTextContent,.footerContainer .mcnTextContent p{
      color:#a5a2b3;
      font-family:Helvetica;
      font-size:12px;
      line-height:125%;
      text-align:left;
    }
    .footerContainer .mcnTextContent a{
      color:#a5a2b3;
      font-weight:normal;
      text-decoration:underline;
    }
    @media only screen and (max-width: 480px){
      body,table,td,p,a,li,blockquote{
        -webkit-text-size-adjust:none !important;
      }

    }	@media only screen and (max-width: 480px){
      body{
        width:100% !important;
        min-width:100% !important;
      }

    }	@media only screen and (max-width: 480px){
      #templateContainer,#templatePreheader,#templateHeader,#templateColumns,#templateBody,#templateFooter{
        max-width:600px !important;
        width:100% !important;
      }

    }	@media only screen and (max-width: 480px){
      .columnsContainer{
        display:block!important;
        max-width:600px !important;
        padding-bottom:18px !important;
        padding-left:0 !important;
        width:100%!important;
      }

    }	@media only screen and (max-width: 480px){
      .mcnImage{
        height:auto !important;
        width:100% !important;
      }

    }	@media only screen and (max-width: 480px){
      .mcnCartContainer,.mcnCaptionTopContent,.mcnRecContentContainer,.mcnCaptionBottomContent,.mcnTextContentContainer,.mcnBoxedTextContentContainer,.mcnImageGroupContentContainer,.mcnCaptionLeftTextContentContainer,.mcnCaptionRightTextContentContainer,.mcnCaptionLeftImageContentContainer,.mcnCaptionRightImageContentContainer,.mcnImageCardLeftTextContentContainer,.mcnImageCardRightTextContentContainer{
        max-width:100% !important;
        width:100% !important;
      }

    }	@media only screen and (max-width: 480px){
      .mcnBoxedTextContentContainer{
        min-width:100% !important;
      }

    }	@media only screen and (max-width: 480px){
      .mcnImageGroupContent{
        padding:9px !important;
      }

    }	@media only screen and (max-width: 480px){
      .mcnCaptionLeftContentOuter .mcnTextContent,.mcnCaptionRightContentOuter .mcnTextContent{
        padding-top:9px !important;
      }

    }	@media only screen and (max-width: 480px){
      .mcnImageCardTopImageContent,.mcnCaptionBlockInner .mcnCaptionTopContent:last-child .mcnTextContent{
        padding-top:18px !important;
      }

    }	@media only screen and (max-width: 480px){
      .mcnImageCardBottomImageContent{
        padding-bottom:9px !important;
      }

    }	@media only screen and (max-width: 480px){
      .mcnImageGroupBlockInner{
        padding-top:0 !important;
        padding-bottom:0 !important;
      }

    }	@media only screen and (max-width: 480px){
      .mcnImageGroupBlockOuter{
        padding-top:9px !important;
        padding-bottom:9px !important;
      }

    }	@media only screen and (max-width: 480px){
      .mcnTextContent,.mcnBoxedTextContentColumn{
        padding-right:18px !important;
        padding-left:18px !important;
      }

    }	@media only screen and (max-width: 480px){
      .mcnImageCardLeftImageContent,.mcnImageCardRightImageContent{
        padding-right:18px !important;
        padding-bottom:0 !important;
        padding-left:18px !important;
      }

    }	@media only screen and (max-width: 480px){
      .mcpreview-image-uploader{
        display:none !important;
        width:100% !important;
      }

    }	@media only screen and (max-width: 480px){
      h1{
        font-size:30px !important;
        line-height:100% !important;
      }

    }	@media only screen and (max-width: 480px){
      h2{
        font-size:20px !important;
        line-height:100% !important;
      }

    }	@media only screen and (max-width: 480px){
      h3{
        font-size:18px !important;
        line-height:100% !important;
      }

    }	@media only screen and (max-width: 480px){
      h4{
        font-size:16px !important;
        line-height:100% !important;
      }

    }	@media only screen and (max-width: 480px){
      .mcnBoxedTextContentContainer .mcnTextContent,.mcnBoxedTextContentContainer .mcnTextContent p{
        font-size:18px !important;
        line-height:125% !important;
      }

    }	@media only screen and (max-width: 480px){
      #templatePreheader{
        display:block !important;
      }

    }	@media only screen and (max-width: 480px){
      .preheaderContainer .mcnTextContent,.preheaderContainer .mcnTextContent p{
        font-size:12px !important;
        line-height:115% !important;
      }

    }	@media only screen and (max-width: 480px){
      .headerContainer .mcnTextContent,.headerContainer .mcnTextContent p{
        font-size:32px !important;
        line-height:125% !important;
      }

    }	@media only screen and (max-width: 480px){
      .bodyContainer .mcnTextContent,.bodyContainer .mcnTextContent p{
        font-size:16px !important;
        line-height:150% !important;
      }

    }	@media only screen and (max-width: 480px){
      .leftColumnContainer .mcnTextContent,.leftColumnContainer .mcnTextContent p{
        font-size:16px !important;
        line-height:150% !important;
      }

    }	@media only screen and (max-width: 480px){
      .rightColumnContainer .mcnTextContent,.rightColumnContainer .mcnTextContent p{
        font-size:16px !important;
        line-height:150% !important;
      }

    }	@media only screen and (max-width: 480px){
      .footerContainer .mcnTextContent,.footerContainer .mcnTextContent p{
        font-size:12px !important;
        line-height:150% !important;
      }

    }</style></head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="height: 100%;margin: 0;padding: 0;width: 100%;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #ffffff;">
<center>
  <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;height: 100%;margin: 0;padding: 0;width: 100%;background-color: #ffffff;">
    <tr>
      <td align="center" valign="top" id="bodyCell" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;height: 100%;margin: 0;padding: 0;width: 100%;border-top: 0;">
        <!-- BEGIN TEMPLATE // -->
        <table border="0" cellpadding="0" cellspacing="0" width="600" id="templateContainer" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;border: 0;">
          <tr>
            <td align="center" valign="top" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
              <!-- BEGIN PREHEADER // -->
              <table border="0" cellpadding="0" cellspacing="0" width="600" id="templatePreheader" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #ffffff;border-top: 0;border-bottom: 0;">
                <tr>
                  <td valign="top" class="preheaderContainer" style="padding-top: 9px;padding-bottom: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                      <tbody class="mcnTextBlockOuter">
                      <tr>
                        <td valign="top" class="mcnTextBlockInner" style="padding-top: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                          <!--[if mso]>
                          <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
                            <tr>
                          <![endif]-->

                          <!--[if mso]>
                          <td valign="top" width="390" style="width:390px;">
                          <![endif]-->
                          <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width: 390px;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" class="mcnTextContentContainer">
                            <tbody><tr>

                              <td valign="top" class="mcnTextContent" style="padding-top: 0;padding-left: 18px;padding-bottom: 9px;padding-right: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;color: #a5a2b3;font-family: Helvetica;font-size: 12px;line-height: 125%;text-align: left;">
@yield('teaser_text')

                              </td>
                            </tr>
                            </tbody></table>
                          <!--[if mso]>
                          </td>
                          <![endif]-->

                          <!--[if mso]>
                          <td valign="top" width="210" style="width:210px;">
                          <![endif]-->
                          <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width: 210px;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" class="mcnTextContentContainer">
                            <tbody><tr>

                              <td valign="top" class="mcnTextContent" style="padding-top: 0;padding-left: 18px;padding-bottom: 9px;padding-right: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;color: #a5a2b3;font-family: Helvetica;font-size: 12px;line-height: 125%;text-align: left;">

                                <a href="@yield('view_in_browser__url')" target="_blank" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #eba124;font-weight: normal;text-decoration: none;">@yield('view_in_browser__text')</a>
                              </td>
                            </tr>
                            </tbody></table>
                          <!--[if mso]>
                          </td>
                          <![endif]-->

                          <!--[if mso]>
                          </tr>
                          </table>
                          <![endif]-->
                        </td>
                      </tr>
                      </tbody>
                    </table></td>
                </tr>
              </table>
              <!-- // END PREHEADER -->
            </td>
          </tr>
          <tr>
            <td align="center" valign="top" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
              <!-- BEGIN HEADER // -->
              <table border="0" cellpadding="0" cellspacing="0" width="600" id="templateHeader" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #ffffff;border-top: 0;border-bottom: 0;">
                <tr>
                  <td valign="top" class="headerContainer" style="padding-top: 9px;padding-bottom: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                      <tbody class="mcnImageBlockOuter">
                      <tr>
                        <td valign="top" style="padding: 0px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" class="mcnImageBlockInner">
                          <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                            <tbody><tr>
                              <td class="mcnImageContent" valign="top" style="padding-right: 0px;padding-left: 0px;padding-top: 0;padding-bottom: 0;text-align: center;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">


                                <img align="center" alt="" src="@yield('hero_image')" width="600" style="max-width: 600px;padding-bottom: 0;display: inline !important;vertical-align: bottom;border: 0;height: auto;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;" class="mcnImage">


                              </td>
                            </tr>
                            </tbody></table>
                        </td>
                      </tr>
                      </tbody>
                    </table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnDividerBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;table-layout: fixed !important;">
                      <tbody class="mcnDividerBlockOuter">
                      <tr>
                        <td class="mcnDividerBlockInner" style="min-width: 100%;padding: 12px 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                          <table class="mcnDividerContent" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                            <tbody><tr>
                              <td style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                                <span></span>
                              </td>
                            </tr>
                            </tbody></table>
                          <!--
                                          <td class="mcnDividerBlockInner" style="padding: 18px;">
                                          <hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;" />
                          -->
                        </td>
                      </tr>
                      </tbody>
                    </table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                      <tbody class="mcnTextBlockOuter">
                      <tr>
                        <td valign="top" class="mcnTextBlockInner" style="padding-top: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                          <!--[if mso]>
                          <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
                            <tr>
                          <![endif]-->

                          <!--[if mso]>
                          <td valign="top" width="600" style="width:600px;">
                          <![endif]-->
                          <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width: 100%;min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" class="mcnTextContentContainer">
                            <tbody><tr>

                              <td valign="top" class="mcnTextContent" style="padding: 0px 18px 9px;color: #3D3A4C;font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;font-size: 32px;font-style: normal;font-weight: normal;line-height: 125%;text-align: left;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;">
@yield('headline_text')
                              </td>
                            </tr>
                            </tbody></table>
                          <!--[if mso]>
                          </td>
                          <![endif]-->

                          <!--[if mso]>
                          </tr>
                          </table>
                          <![endif]-->
                        </td>
                      </tr>
                      </tbody>
                    </table></td>
                </tr>
              </table>
              <!-- // END HEADER -->
            </td>
          </tr>
          <tr>
            <td align="center" valign="top" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
              <!-- BEGIN BODY // -->
              <table border="0" cellpadding="0" cellspacing="0" width="600" id="templateBody" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #ffffff;border-top: 0;border-bottom: 0;">
                <tr>
                  <td valign="top" class="bodyContainer" style="padding-top: 9px;padding-bottom: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                    {{--<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnDividerBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;table-layout: fixed !important;">--}}
                      {{--<tbody class="mcnDividerBlockOuter">--}}
                      {{--<tr>--}}
                        {{--<td class="mcnDividerBlockInner" style="min-width: 100%;padding: 15px 18px 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">--}}
                          {{--<table class="mcnDividerContent" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width: 100%;border-top-width: 1px;border-top-style: solid;border-top-color: #EAEAEA;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">--}}
                            {{--<tbody><tr>--}}
                              {{--<td style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">--}}
                                {{--<span></span>--}}
                              {{--</td>--}}
                            {{--</tr>--}}
                            {{--</tbody></table>--}}
                          {{--<!----}}
                                          {{--<td class="mcnDividerBlockInner" style="padding: 18px;">--}}
                                          {{--<hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;" />--}}
                          {{---->--}}
                        {{--</td>--}}
                      {{--</tr>--}}
                      {{--</tbody>--}}
                    {{--</table>--}}
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnDividerBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;table-layout: fixed !important;">
                      <tbody class="mcnDividerBlockOuter">
                      <tr>
                        <td class="mcnDividerBlockInner" style="min-width: 100%;padding: 18px 18px 0px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                          <table class="mcnDividerContent" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width: 100%;border-top-width: 1px;border-top-style: solid;border-top-color: #EAEAEA;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                            <tbody><tr>
                              <td style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                                <span></span>
                              </td>
                            </tr>
                            </tbody></table>
                          <!--
                                          <td class="mcnDividerBlockInner" style="padding: 18px;">
                                          <hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;" />
                          -->
                        </td>
                      </tr>
                      </tbody>
                    </table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                      <tbody class="mcnTextBlockOuter">
                      <tr>
                        <td valign="top" class="mcnTextBlockInner" style="padding-top: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                          <!--[if mso]>
                          <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
                            <tr>
                          <![endif]-->

                          <!--[if mso]>
                          <td valign="top" width="600" style="width:600px;">
                          <![endif]-->
                          <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width: 100%;min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" class="mcnTextContentContainer">
                            <tbody><tr>

                              <td valign="top" class="mcnTextContent" style="padding-top: 0;padding-right: 18px;padding-bottom: 9px;padding-left: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;color: #3d3a4c;font-family: Helvetica;font-size: 16px;line-height: 150%;text-align: left;">
@yield('content')
{{--style for AHREF: style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #0090db;font-weight: normal;text-decoration: none;"--}}
                              </td>
                            </tr>
                            </tbody></table>
                          <!--[if mso]>
                          </td>
                          <![endif]-->

                          <!--[if mso]>
                          </tr>
                          </table>
                          <![endif]-->
                        </td>
                      </tr>
                      </tbody>
                    </table></td>
                </tr>
              </table>
              <!-- // END BODY -->
            </td>
          </tr>
          <tr>
            <td align="center" valign="top" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
              <!-- BEGIN COLUMNS // -->
              <table border="0" cellpadding="0" cellspacing="0" width="600" id="templateColumns" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #ffffff;border-top: 0;border-bottom: 0;">
                <tr>
                  <td align="left" valign="top" class="columnsContainer" width="50%" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="templateColumn" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                      <tr>
                        <td valign="top" class="leftColumnContainer" style="padding-top: 9px;padding-bottom: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"></td>
                      </tr>
                    </table>
                  </td>
                  <td align="left" valign="top" class="columnsContainer" width="50%" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="templateColumn" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                      <tr>
                        <td valign="top" class="rightColumnContainer" style="padding-top: 9px;padding-bottom: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"></td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <!-- // END COLUMNS -->
            </td>
          </tr>
          <tr>
            <td align="center" valign="top" style="padding-bottom: 40px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
              <!-- BEGIN FOOTER // -->
              <table border="0" cellpadding="0" cellspacing="0" width="600" id="templateFooter" style="border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #ffffff;border-top: 0;border-bottom: 0;">
                <tr>
                  <td valign="top" class="footerContainer" style="padding-top: 9px;padding-bottom: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnDividerBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;table-layout: fixed !important;">
                      <tbody class="mcnDividerBlockOuter">
                      <tr>
                        <td class="mcnDividerBlockInner" style="min-width: 100%;padding: 0px 18px 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                          <table class="mcnDividerContent" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width: 100%;border-top-width: 1px;border-top-style: solid;border-top-color: #EAEAEA;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                            <tbody><tr>
                              <td style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                                <span></span>
                              </td>
                            </tr>
                            </tbody></table>
                          <!--
                                          <td class="mcnDividerBlockInner" style="padding: 18px;">
                                          <hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;" />
                          -->
                        </td>
                      </tr>
                      </tbody>
                    </table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                      <tbody class="mcnTextBlockOuter">
                      <tr>
                        <td valign="top" class="mcnTextBlockInner" style="padding-top: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
                          <!--[if mso]>
                          <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
                            <tr>
                          <![endif]-->

                          <!--[if mso]>
                          <td valign="top" width="600" style="width:600px;">
                          <![endif]-->
                          <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width: 100%;min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" class="mcnTextContentContainer">
                            <tbody><tr>

                              <td valign="top" class="mcnTextContent" style="padding: 0px 18px 9px;color: #A5A2B3;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;font-family: Helvetica;font-size: 12px;line-height: 125%;text-align: left;">

                                <em>Copyright © {{ date('Y') }} {{ config('site.community_long_name') }}, All rights reserved.</em><br>
                                <br>
                                You are receiving this email because you attended (or have registered to attend) a Tres Dias event or requested to be included in correspondence with us.
                                We use your email only to inform you about Tres Dias events in the {{ config('site.community_acronym') }} community.
                                We will never sell or give out your personal information.
                                NOTE: our emails come mostly in Sept/Oct and March/April, and quieter through the rest of the year.
                                If you wish to be excluded from future emails about Tres Dias and the {{ config('site.community_long_name') }} community,
                                please reply with "Unsubscribe" in the subject line, or change your notification settings in your profile on the website .
                                Please understand that unsubscribing may mean you miss out on important community information.<br>
                                <br>
                                <strong>Our mailing address is:</strong><br>
                                {{ config('site.community_long_name') }}<br>
{!! nl2br(e(config('site.community_mailing_address'))) !!}

                              </td>
                            </tr>
                            </tbody></table>
                          <!--[if mso]>
                          </td>
                          <![endif]-->

                          <!--[if mso]>
                          </tr>
                          </table>
                          <![endif]-->
                        </td>
                      </tr>
                      </tbody>
                    </table></td>
                </tr>
              </table>
              <!-- // END FOOTER -->
            </td>
          </tr>
        </table>
        <!-- // END TEMPLATE -->
      </td>
    </tr>
  </table>
</center>
</body>
</html>
