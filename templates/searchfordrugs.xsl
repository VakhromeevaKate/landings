<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:import href="../../templates/common.xsl"/>
    <xsl:template match="block[@name='searchfordrugs']">
        <h2>Поиск лекарств</h2>

        <form method="get" autocomplete="off" id="SearchForDrugs_Form">
            <div>
                <input type="text" maxlength="255" placeholder="Введите название товара"/>
            </div>
            <div>
                <input type="text" maxlength="255" placeholder="Введите название товара"/>
            </div>
        </form>

        <xsl:choose>

            <xsl:when test="@type='finding'">
                <!--xsl:call-template name="drugs_list"/-->
                <p>Что-то ищем</p>
            </xsl:when>

            <xsl:when test="@type='found'">
                <p>Чего-то нашли</p>
            </xsl:when>

            <xsl:when test="@type='nothing_found'">
                <p>К сожалению, ничего не найдено.</p>
            </xsl:when>

            <xsl:when test="@type='nothing_entered'">
                <p>Не введены условия поиска.</p>
            </xsl:when>
            <xsl:otherwise>
                <p>Какой-то странный тип не из списка.</p>
            </xsl:otherwise>

        </xsl:choose>
    </xsl:template>
</xsl:stylesheet>