<?php

namespace Davichano\DNI;

final class Endpoints
{
    const urlCaptcha = "http://ww4.essalud.gob.pe:7777/acredita/captcha.jpg";
    const urlSearch = "http://ww4.essalud.gob.pe:7777/acredita/servlet/Ctrlwacre?pg=1&ll=Libreta+Electoral%2FDNI&td=1&nd={0}&submit=Consultar&captchafield_doc={1}";
    const urlOCR = 'https://api.ocr.space/parse/image';
}