<?xml version="1.0" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	 version="1.1">

<xsl:template match="/">

  <xsl:apply-templates/>

</xsl:template>

<xsl:template match="div2">
  <xsl:element name="div2">
    <xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
    <xsl:attribute name="n"><xsl:value-of select="@n"/></xsl:attribute>
    <xsl:attribute name="type"><xsl:value-of select="@type"/></xsl:attribute>

   
    <xsl:element name="docAuthor">
      <xsl:value-of select="byline"/>
      <!-- for tynan volume (flower.xml) only -->
      <xsl:value-of select="/TEI.2/teiHeader/fileDesc/titleStmt/author"/> 
    </xsl:element>
    <xsl:apply-templates/>
  </xsl:element>
</xsl:template>


<xsl:template match="@*|node()">
	<xsl:copy>
	<xsl:apply-templates select="@*|node()"/>
	</xsl:copy>
</xsl:template>

</xsl:stylesheet>