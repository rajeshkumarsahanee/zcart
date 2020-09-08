function fillForm(from_url, html, category) {       
    if(from_url.indexOf("https://www.flipkart.com") >= 0) {
        switch(category.toLowerCase()) {
            case "mobiles": flipkartMobileExtractFill(html);
                break;
            case "tablets": ;
                break;
            case "laptops": ;
                break;
            case "televisions": ;
                break;
            case "cameras": ;
                break;
        }        
    } else {
        alert("can't fetch from this site");
    }
}

function flipkartMobileExtractFill(html) {
    var htmlElement = document.createElement('html');
    htmlElement.innerHTML = html;
    //fillBrowserForm("input", "name", $(htmlElement).find("h1._3eAQiD").first().text());        
    fillBrowserForm("input", "sku", $(htmlElement).find("h1.title").first().text());
    fillBrowserForm("input", "name", $(htmlElement).find("h1").first().text());
    fillBrowserForm("input", "slug", $(htmlElement).find("h1.title").first().text());
    fillBrowserForm("textarea", "short_description", $(htmlElement).find("div._2PF8IO").first().text());
    fillBrowserForm("textarea", "long_description", $(htmlElement).find("div._2PF8IO").first().text());
    fillBrowserForm("textarea", "images", $(htmlElement).find("img.productImage").first().attr("data-zoomimage"));
    fillBrowserForm("input", "1", $(htmlElement).find("tr:contains(Brand) > td.specsValue").first().text());
    fillBrowserForm("input", "2", $(htmlElement).find("tr:contains(Handset Color) > td.specsValue").first().text());
    fillBrowserForm("input", "23", $(htmlElement).find("tr:contains(Form) > td.specsValue").first().text());
    fillBrowserForm("input", "24", $(htmlElement).find("tr:contains(SIM Size) > td.specsValue").first().text());
    fillBrowserForm("input", "59", $(htmlElement).find("tr:contains(Call Features) > td.specsValue").first().text());
    fillBrowserForm("input", "25", $(htmlElement).find("tr:contains(Model Name) > td.specsValue").first().text());
    fillBrowserForm("input", "26", $(htmlElement).find("tr:contains(Touch Screen) > td.specsValue").first().text());
    fillBrowserForm("input", "27", $(htmlElement).find("tr:contains(SIM Type) > td.specsValue").first().text());
    fillBrowserForm("input", "51", $(htmlElement).find("tr:contains(In the Box) > td.specsValue").first().text());
    fillBrowserForm("input", "31", $(htmlElement).find("tr:contains(Video Player) > td.specsValue").first().text());
    fillBrowserForm("input", "32", $(htmlElement).find("tr:contains(Music Player) > td.specsValue").first().text());
    fillBrowserForm("input", "29", $(htmlElement).find("tr:contains(Video Recording) > td.specsValue").first().text());
    fillBrowserForm("input", "60", $(htmlElement).find("tr:contains(Secondary Camera Features) > td.specsValue").first().text());
    fillBrowserForm("input", "21", $(htmlElement).find("tr:contains(Flash) > td.specsValue").first().text());
    fillBrowserForm("input", "30", $(htmlElement).find("tr:contains(HD Recording) > td.specsValue").first().text());
    fillBrowserForm("input", "19", $(htmlElement).find("tr:contains(Rear Camera) > td.specsValue").first().text());
    fillBrowserForm("input", "20", $(htmlElement).find("tr:contains(Front Facing Camera) > td.specsValue").first().text());
    fillBrowserForm("input", "61", $(htmlElement).find("tr:contains(Primary Camera Features) > td.specsValue").first().text());
    fillBrowserForm("input", "33", $(htmlElement).find("tr:contains(Network Type) > td.specsValue").first().text());
    fillBrowserForm("input", "34", $(htmlElement).find("tr:contains(Audio Jack) > td.specsValue").first().text());
    fillBrowserForm("input", "62", $(htmlElement).find("tr:contains(Preinstalled Browser) > td.specsValue").first().text());
    fillBrowserForm("input", "35", $(htmlElement).find("tr:contains(Bluetooth) > td.specsValue").first().text());
    fillBrowserForm("input", "63", $(htmlElement).find("tr:contains(Navigation Technology) > td.specsValue").first().text());
    fillBrowserForm("input", "36", $(htmlElement).find("tr:contains(Wifi) > td.specsValue").first().text());
    fillBrowserForm("input", "64", $(htmlElement).find("tr:contains(Internet Features) > td.specsValue").first().text());
    fillBrowserForm("input", "37", $(htmlElement).find("tr:contains(GPRS) > td.specsValue").first().text());
    fillBrowserForm("input", "39", $(htmlElement).find("tr:contains(Tethering) > td.specsValue").first().text());
    fillBrowserForm("input", "38", $(htmlElement).find("tr:contains(USB Connectivity) > td.specsValue").first().text());
    fillBrowserForm("input", "22", $(htmlElement).find("tr:contains(Sensors) > td.specsValue").first().text());
    fillBrowserForm("input", "65", $(htmlElement).find("tr:contains(Call Memory) > td.specsValue").first().text());
    fillBrowserForm("input", "40", $(htmlElement).find("tr:contains(SAR Value) > td.specsValue").first().text());
    fillBrowserForm("input", "66", $(htmlElement).find("tr:contains(Important Apps) > td.specsValue").first().text());
    fillBrowserForm("input", "67", $(htmlElement).find("tr:contains(Additional Features) > td.specsValue").first().text());
    fillBrowserForm("input", "42", $(htmlElement).find("tr:contains(Warranty Summary) > td.specsValue").first().text());
    fillBrowserForm("input", "41", $(htmlElement).find("table:contains(Dimensions) tr:contains(Weight) > td.specsValue").first().text());
    fillBrowserForm("input", "4", $(htmlElement).find("table:contains(Dimensions) tr:contains(Size) > td.specsValue").first().text());
    fillBrowserForm("input", "16", $(htmlElement).find("tr:contains(Resolution) > td.specsValue").first().text());
    
}

function fillBrowserForm(tag, name, value) {    
    if (tag === "input" || tag !== "textarea") {
        document.getElementsByName(name)[0].value = value;
    }
    if (tag === "textarea") {
        document.getElementsByName(name)[0].innerHTML = value;
    }
}