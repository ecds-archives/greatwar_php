<?xml version="1.0" encoding="ISO-8859-1"?>  

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
	xmlns:html="http://www.w3.org/TR/REC-html40" 
	xmlns:ino="http://namespaces.softwareag.com/tamino/response2" 
	xmlns:xql="http://metalab.unc.edu/xql/">

<xsl:param name="mode">full</xsl:param>  	
	<!-- options: thumbnail, thumbdesc, full, zoom -->
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
    <xsl:when test="$mode='full'">
       <xsl:apply-templates select="//figure" mode="full"/>
    </xsl:when>
    <xsl:when test="$mode='zoom'">
       <xsl:apply-templates select="//figure" mode="zoom"/>
    </xsl:when>
  </xsl:choose>

</xsl:template> 

<!-- thumbnail and title only -->
<xsl:template match="figure" mode="thumbnail">
  <table class="thumbnail">
   <tr><td>
    <a class="img">
      <xsl:attribute name="href">postcards/view.php?id=<xsl:value-of select="@entity"/></xsl:attribute>
    <xsl:element name="img">
	<xsl:attribute name="class">thumbnail</xsl:attribute>
	<xsl:attribute name="src">http://chaucer.library.emory.edu/wwi/images/thumbnail/<xsl:value-of select="@entity"/>.jpg</xsl:attribute>
    </xsl:element>
    </a>
   </td></tr>
   <tr><td class="title">
	<xsl:value-of select="head"/>
   </td></tr></table>
</xsl:template>

<!-- thumbnail and full text description, side by side -->
<xsl:template match="figure" mode="thumbdesc">
   <tr><td>
    <a class="img">
      <xsl:attribute name="href">postcards/view.php?id=<xsl:value-of select="@entity"/></xsl:attribute>
    <xsl:element name="img">
	<xsl:attribute name="class">thumbnail</xsl:attribute>
	<xsl:attribute name="src"><xsl:value-of select="concat('http://chaucer.library.emory.edu/wwi/images/thumbnail/', @entity, '.jpg')"/></xsl:attribute>
    </xsl:element>
    </a>
   </td>
   <td class="description">
     <xsl:call-template name="figure-description"/>
   </td></tr>
</xsl:template>

<!-- full-size image and full text description, side by side -->
<xsl:template match="figure" mode="full">
   <table>
   <tr><td>
    <a class="img">
     <xsl:attribute name="href">postcards/view.php?id=<xsl:value-of select="@entity"/>&amp;zoom=2</xsl:attribute>
    <xsl:element name="img">
	<xsl:attribute name="src"><xsl:value-of select="concat('http://chaucer.library.emory.edu/wwi/images/realsize/', @entity, '.jpg')"/></xsl:attribute>
    </xsl:element>
    </a>
   </td>

   <td class="description">
     <xsl:call-template name="figure-description"/>
   
   <p><a>
      <xsl:attribute name="href">postcards/view.php?id=<xsl:value-of
select="@entity"/>&amp;zoom=2</xsl:attribute>
	View larger image
     </a>
   </p>
   </td></tr>
   </table>
</xsl:template>


<!-- print out full description & category information -->
<xsl:template name="figure-description">
      <xsl:apply-templates select="head"/>
      <xsl:apply-templates select=".//figDesc"/>

  <!-- display any text, but only in full display mode -->
   <xsl:if test="$mode='full'">
      <xsl:apply-templates select="p"/>
   </xsl:if>

      <h5>Categories:</h5>
        <ul>
         <xsl:call-template name="interp-names">
           <xsl:with-param name="list"><xsl:value-of
			select="@ana"/></xsl:with-param>
	 </xsl:call-template>
        </ul>

</xsl:template>

<!-- double-size image with title only -->
<xsl:template match="figure" mode="zoom">

   <xsl:apply-templates select="head"/>

    <xsl:element name="img">
	<xsl:attribute name="src"><xsl:value-of select="concat('http://chaucer.library.emory.edu/wwi/images/doublesize/', @entity, '.jpg')"/></xsl:attribute>
    </xsl:element>

   <p><a>
      <xsl:attribute name="href">postcards/view.php?id=<xsl:value-of select="@entity"/></xsl:attribute>
	View full details
     </a>
   </p>



</xsl:template>




<xsl:template match="head">
  <h4><xsl:apply-templates/></h4>
</xsl:template>

<xsl:template match="figDesc">
  <p><xsl:apply-templates/></p>
</xsl:template>

<xsl:template match="text">
 <p class="figure-text"><xsl:apply-templates/></p>
</xsl:template>

<xsl:template match="l">
  <xsl:apply-templates/><br/>
</xsl:template>


<!-- keys to access human readable interp categories & values -->
<xsl:key name="interp-name" match="//interp/@value" use="../@id"/>
<xsl:key name="interp-cat" match="//interpGrp/@type" use="../interp/@id"/>

<!-- convert category ids into names -->
<xsl:template name="interp-names">
  <xsl:param name="list"/>

<!-- If list contains a space, there is more than one category label;
     if so, split the list on the first space and recurse. -->
      <xsl:if test="contains($list, ' ')">
 	<xsl:call-template name="interp-names">
 	   <xsl:with-param name="list" select="substring-after($list, ' ')"/>
	</xsl:call-template>
      </xsl:if> 

<!-- get the current id: either string before the first space, or the
     whole string (in the deepest recursion) -->
    <xsl:variable name="id">
     <xsl:choose>
       <xsl:when test="contains($list, ' ')">
	  <xsl:value-of select="substring-before($list, ' ')"/>
       </xsl:when>
       <xsl:otherwise>
         <xsl:value-of select="$list"/>
       </xsl:otherwise>
     </xsl:choose>
    </xsl:variable>

     <xsl:if test="$id != ''">
        <li><xsl:value-of select="key('interp-cat', $id)"/>: 
	    <xsl:value-of select="key('interp-name', $id)"/></li>
     </xsl:if>


</xsl:template>

</xsl:stylesheet>
