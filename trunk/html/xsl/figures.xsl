<?xml version="1.0" encoding="ISO-8859-1"?>  

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
	xmlns:html="http://www.w3.org/TR/REC-html40" 
	xmlns:ino="http://namespaces.softwareag.com/tamino/response2" 
	xmlns:xql="http://metalab.unc.edu/xql/">

<xsl:param name="mode">thumbdesc</xsl:param>  	<!-- options: thumbnail, thumbdesc -->
<xsl:param name="interp">0</xsl:param>  

<xsl:output method="html"/>  

<xsl:template match="/"> 

  <xsl:choose>
    <xsl:when test="$mode='thumbnail'">
       <xsl:apply-templates select="//figure" mode="thumbnail"/>
    </xsl:when>
    <xsl:when test="$mode='thumbdesc'">
      <table class="thumbnail">
       <xsl:apply-templates select="//figure" mode="thumbdesc"/>
      </table>
    </xsl:when>
  </xsl:choose>

</xsl:template> 

<!-- thumbnail and title only -->
<xsl:template match="figure" mode="thumbnail">
  <table class="thumbnail">
   <tr><td>
    <xsl:element name="img">
	<xsl:attribute name="class">thumbnail</xsl:attribute>
	<xsl:attribute name="src">http://chaucer.library.emory.edu/wwi/images/thumbnail/<xsl:value-of select="@entity"/>.jpg</xsl:attribute>
    </xsl:element>
   </td></tr>
   <tr><td class="title">
	<xsl:value-of select="head"/>
   </td></tr></table>
</xsl:template>

<!-- thumbnail and full text description, side by side -->
<xsl:template match="figure" mode="thumbdesc">
   <tr><td>
    <xsl:element name="img">
	<xsl:attribute name="class">thumbnail</xsl:attribute>
	<xsl:attribute name="src">http://chaucer.library.emory.edu/wwi/images/thumbnail/<xsl:value-of select="@entity"/>.jpg</xsl:attribute>
    </xsl:element>
   </td>

   <td class="description">
      <xsl:apply-templates select="head"/>
      <xsl:apply-templates select=".//figDesc"/>

      <h5>Categories:</h5>
         <xsl:call-template name="interp-names">
           <xsl:with-param name="list"><xsl:value-of
			select="@ana"/></xsl:with-param>
	</xsl:call-template>

   </td></tr>
</xsl:template>

<xsl:template match="head">
  <h4><xsl:apply-templates/></h4>
</xsl:template>

<xsl:template match="figDesc">
  <p><xsl:apply-templates/></p>
</xsl:template>


<xsl:key name="interpid" match="//interp/@value" use="../@id"/>

<!-- convert category ids into names -->
<xsl:template name="interp-names">
  <xsl:param name="list"/>

<!-- if list contains a space, there is more than one category id -->
      <xsl:if test="contains($list, ' ')">
 	<xsl:call-template name="interp-names">
 	   <xsl:with-param name="list" select="substring-after($list, ' ')"/>
	</xsl:call-template>
      </xsl:if>
  
     <xsl:variable name="id"><xsl:value-of 
		select="substring-before($list, ' ')"/></xsl:variable>

     <xsl:if test="$id != ''">
        <li><xsl:value-of select="key('interpid', $id)"/></li>
     </xsl:if>


</xsl:template>

</xsl:stylesheet>
