<?xml version="1.0" encoding="ISO-8859-1"?>  

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
	xmlns:html="http://www.w3.org/TR/REC-html40" 
	xmlns:xql="http://metalab.unc.edu/xql/">

<!-- Note: all links in this stylesheet should be relative to the root
     directory of the entire site. -->

<xsl:include href="teipoetry.xsl"/>
<xsl:include href="footnotes.xsl"/>
<xsl:include href="convertcase.xsl"/>

<xsl:param name="mode"/>
<xsl:param name="selflink"/>	<!-- for use with footnotes -->	
<xsl:param name="max"/>
<xsl:param name="position"/>

<!-- options:
     poem = full-text of a single poem (or poem-level item)
     bibl = format bibl from sourceDesc
-->

<xsl:output method="xml"/>  

<xsl:template match="/">
<xsl:apply-templates />
</xsl:template>

<xsl:template match="div" mode="poem">

   <!-- ignore docAuthor in poem mode -->
   <xsl:apply-templates select="*[not(self::docAuthor)]"/> 

  <xsl:call-template name="endnotes"/>
</xsl:template>

<xsl:template match="div/docAuthor"/> <!-- don't display the div level docAuthor -->

<!-- offset div3 poems a little, and give them a name anchor to link to -->
<!-- <xsl:template match="div/div[@type='poem']">
 <div class="poem">
    <a>
   <xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
  </a>
  <xsl:apply-templates select="*[not(self::docAuthor)]"/>  
 </div>

</xsl:template>
-->



</xsl:stylesheet>