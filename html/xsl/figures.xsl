<?xml version="1.0" encoding="ISO-8859-1"?>  

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
	xmlns:html="http://www.w3.org/TR/REC-html40" 
	xmlns:ino="http://namespaces.softwareag.com/tamino/response2" 
	xmlns:xql="http://metalab.unc.edu/xql/">

<xsl:output method="html"/>  

<xsl:template match="/"> 
  <xsl:apply-templates select="//figure" />

</xsl:template> 


<xsl:template match="figure">
  <table class="thumbnail"><tr><td>
    <xsl:element name="img">
	<xsl:attribute name="class">thumbnail</xsl:attribute>
	<xsl:attribute name="src">http://chaucer.library.emory.edu/wwi/images/thumbnail/<xsl:value-of select="@entity"/>.jpg</xsl:attribute>
    </xsl:element>
     <!-- <br/> -->
   </td></tr><tr><td class="description">
	<xsl:value-of select="head"/>
   </td></tr></table>
</xsl:template>

</xsl:stylesheet>
