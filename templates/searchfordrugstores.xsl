<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:import href="../../templates/common.xsl"/>
    <xsl:template match="block[@name='searchfordrugstores']">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
        <script src="http://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
        <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
        <script src="/landings/js/hints.js?7" type="text/javascript"></script>

        <link href="/landings/css/landings.css?7" rel="stylesheet" charset="utf-8"/>

        <xsl:variable
                name="cityFilter"
                select="../block[@name='searchfordrugstores']/filter/city"/>
        <xsl:variable
                name="cityGenitive"
                select="../block[@name='searchfordrugstores']/filter/genitive"/>
        <xsl:variable
                name="cityPrepositional"
                select="../block[@name='searchfordrugstores']/filter/prepositional"/>
        <xsl:variable
                name="metroFilter"
                select="../block[@name='searchfordrugstores']/filter/metro"/>
        <xsl:variable
                name="addressFilter"
                select="../block[@name='searchfordrugstores']/filter/address"/>
        <xsl:variable
                name="isOpenedNow"
                select="../block[@name='searchfordrugstores']/filter/opened"/>
        <xsl:variable
                name="everytimeFilter"
                select="../block[@name='searchfordrugstores']/filter/everytime"/>

        <h1 class="NoTopMargin">
            <xsl:value-of select="../block[@name='searchfordrugstores']/pageInfo/PageTitle"/>
        </h1>
        <div class="SearchDiv">


            <div class="Container">
                <nobr>
                    <label>Город:</label>
                        <select id="apteki_city">
                            <xsl:for-each select="../block[@name='searchfordrugstores']/cities_list/item">
                                <xsl:if test="name='Москва'">
                                    <option value="{translit}" class="city_option">
                                        <xsl:if test="name = $cityFilter">
                                            <xsl:attribute name="selected">selected</xsl:attribute>
                                        </xsl:if>
                                        <xsl:value-of select="name" disable-output-escaping="yes"/>
                                    </option>
                                </xsl:if>
                                <xsl:if test="name='Санкт-Петербург'">
                                    <option value="{translit}" class="city_option">
                                        <xsl:if test="name = $cityFilter">
                                            <xsl:attribute name="selected">selected</xsl:attribute>
                                        </xsl:if>
                                        <xsl:value-of select="name" disable-output-escaping="yes"/>
                                    </option>
                                </xsl:if>
                            </xsl:for-each>
                            <xsl:for-each select="../block[@name='searchfordrugstores']/regions_list/item">
                                <xsl:variable name="regId"
                                              select="RegionId"/>
                                <xsl:if test="RegionName!='Санкт-Петербург'">
                                <optgroup label="{RegionName}">
                                    <xsl:for-each select="../../../block[@name='searchfordrugstores']/cities_list/item">
                                        <xsl:if test="region_id=$regId and name!='Москва' and name!='Санкт-Петербург'">
                                            <option value="{translit}" class="city_option">
                                                <xsl:if test="name = $cityFilter">
                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                <xsl:value-of select="name" disable-output-escaping="yes"/>
                                            </option>
                                        </xsl:if>
                                    </xsl:for-each>
                                </optgroup>
                                </xsl:if>
                            </xsl:for-each>
                        </select>
                </nobr>
            </div>
            <div class="Container">
                <xsl:choose>
                    <xsl:when test="../block[@name='searchfordrugstores']/metro_list/@isMetro='yes'">
                        <xsl:attribute name="style">visibility:visible</xsl:attribute>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:attribute name="style">display:none</xsl:attribute>
                    </xsl:otherwise>
                </xsl:choose>
                <nobr>
                    <label>Рядом с метро:</label>
                    <select id="apteki_metro">
                        <option value="">Выберите станцию</option>
                        <xsl:for-each select="../block[@name='searchfordrugstores']/metro_list/item">
                            <option value="{Translit}">
                                <xsl:if test="MetroName = $metroFilter">
                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                </xsl:if>
                                <xsl:value-of select="MetroName" disable-output-escaping="yes"/>
                            </option>
                        </xsl:for-each>
                    </select>
                </nobr>
            </div>
            <div class="Container">
                <nobr>
                    <input type="checkbox" id="apteki_24h" class="checkbox" value="true">
                        <xsl:if test="$everytimeFilter='true'">
                            <xsl:attribute name="checked">
                                checked
                            </xsl:attribute>
                        </xsl:if>
                    </input>
                    <label>Круглосуточные аптеки</label>
                </nobr>
                <nobr>
                    <!--input type="checkbox" id="OpenedNow" name="OpenedNow" class="checkbox" value="true">
                        <xsl:if test="$isOpenedNow='true'">
                            <xsl:attribute name="checked">
                                checked
                            </xsl:attribute>
                        </xsl:if>
                    </input-->
                    <!--label>Аптеки, открытые сейчас</label-->
                </nobr>
            </div>
            <div>
                <input id="SearchBox" name="street" class="SearchBox" type="search" maxlength="57" placeholder="Введите адрес">
                    <xsl:attribute name="value">
                        <xsl:value-of select="$addressFilter" disable-output-escaping="yes" />
                    </xsl:attribute>
                </input>
                <div id="HintDiv" class="HintDiv"></div>
                <button id="SearchButton" class="SearchButton" type="button">ПОИСК</button>
                <button id="ClearButton" class="ClearButton" type="button"><nobr>СБРОСИТЬ</nobr></button>
            </div>
            <div>
                <!--label id="ShowNearestLabel" class="ShowNearestLabel">Показать ближайшую аптеку к этой улице</label-->
            </div>
        </div>
        <xsl:choose>
            <xsl:when test="@type='found'">
                <xsl:call-template name="drugstores_list"/>
            </xsl:when>
            <xsl:when test="@type='nothing_found'">
                <xsl:call-template name="nothing_found"/>
            </xsl:when>
            <xsl:when test="@type='nothing_entered'">
                <xsl:call-template name="new_search"/>
            </xsl:when>
            <xsl:otherwise>
                <p>Что-то пошло не так. Зайдите позже.</p>
                <xsl:call-template name="footer"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template name="new_search">
        <xsl:call-template name="footer"/>
    </xsl:template>

    <xsl:template name="drugstores_list">

        <xsl:variable
                name="cityFilter"
                select="../block[@name='searchfordrugstores']/filter/city"/>
        <xsl:variable
                name="cityGenitive"
                select="../block[@name='searchfordrugstores']/filter/genitive"/>
        <span id="SelectedCity" style="visibility:hidden;">
            <xsl:value-of select="$cityFilter" disable-output-escaping="yes"/>
        </span>
        <span id="SelectedCityZoom" style="visibility:hidden;">
            <xsl:value-of select="../block[@name='searchfordrugstores']/filter/zoom"/>
        </span>
        <h2>Аптеки на карте
            <span>
                <xsl:value-of select="$cityGenitive" disable-output-escaping="yes"/>
            </span>
        </h2>
        <div class="MapWrapper">
        <div id="YMapsID"></div>
        <div id="LandingStoresList" class="LandingStoresList">
            <xsl:for-each select="../block[@name='searchfordrugstores']/item">
                <div class="LandingStoresListInner">
                    <div class="name">
                        <xsl:value-of select="StoreName" disable-output-escaping="yes"/>
                    </div>
                    <div>
                        <xsl:value-of select="StoreAddress" disable-output-escaping="yes"/>
                    </div>
                    <div>
                        <div class="tel"><xsl:value-of select="StorePhone1" disable-output-escaping="yes"/></div>
                        <div class="tel"><xsl:value-of select="StorePhone2" disable-output-escaping="yes"/></div>
                        <div class="tel"><xsl:value-of select="StorePhone3" disable-output-escaping="yes"/></div>
                    </div>
                    <div>
                        <xsl:value-of select="StoreWorktime"/>
                    </div>
                    <div>
                        <a href="/stores/{id}">Поиск лекарств в аптеке</a>
                    </div>
                </div>
            </xsl:for-each>
            <script>
                var ymaps_json = '<xsl:value-of select="json" disable-output-escaping="yes"/>';
                var zoom = '<xsl:value-of select="../block[@name='searchfordrugstores']/filter/zoom" disable-output-escaping="yes"/>';
                var region = '<xsl:value-of select="../block[@name='searchfordrugstores']/filter/region" disable-output-escaping="yes"/>'
                var mapcenter = [<xsl:value-of select="../block[@name='searchfordrugstores']/filter/mapcenter" disable-output-escaping="yes"/>]
            </script>
            <script src="/landings/js/ymaps.js?7" type="text/javascript"></script>
        </div>
        </div>
        <xsl:call-template name="footer"/>
    </xsl:template>

    <xsl:template name="nothing_found">
        <xsl:variable
                name="cityGenitive"
                select="../block[@name='searchfordrugstores']/filter/genitive"/>
        <xsl:variable
                name="cityFilter"
                select="../block[@name='searchfordrugstores']/filter/city"/>
        <span id="SelectedCity" style="visibility:hidden;">
            <xsl:value-of select="$cityFilter" disable-output-escaping="yes"/>
        </span>
        <span id="SelectedCityZoom" style="visibility:hidden;">
            <xsl:value-of select="../block[@name='searchfordrugstores']/filter/zoom" disable-output-escaping="yes"/>
        </span>
        <h2>Аптеки на карте
            <span>
                <xsl:value-of select="$cityGenitive"/>
            </span>
        </h2>
        <div class="MapWrapper">
            <div id="YMapsID"></div>
            <div id="LandingStoresList" class="LandingStoresList">
                    <div class="LandingStoresListInner">
                        По Вашему запросу аптек не найдено.
                    </div>
                <script>
                    var ymaps_json = '<xsl:value-of select="json" disable-output-escaping="yes"/>';
                    var zoom = '<xsl:value-of select="../block[@name='searchfordrugstores']/filter/zoom" disable-output-escaping="yes"/>';
                    var region = '<xsl:value-of select="../block[@name='searchfordrugstores']/filter/region" disable-output-escaping="yes"/>'
                    var mapcenter = [<xsl:value-of select="../block[@name='searchfordrugstores']/filter/mapcenter" disable-output-escaping="yes"/>]
                </script>
                <script src="/landings/js/ymaps.js?7" type="text/javascript"></script>
            </div>
        </div>
    </xsl:template>

    <xsl:template name="footer">
        <div class="TextDiv">
            <xsl:value-of select="../block[@name='searchfordrugstores']/pageInfo/PageText" disable-output-escaping="yes"/>
        </div>
    </xsl:template>

</xsl:stylesheet>