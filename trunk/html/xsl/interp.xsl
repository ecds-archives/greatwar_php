<?xml version="1.0" encoding="ISO-8859-1"?>  

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
	xmlns:html="http://www.w3.org/TR/REC-html40" 
	xmlns:ino="http://namespaces.softwareag.com/tamino/response2" 
	xmlns:xql="http://metalab.unc.edu/xql/">


<xsl:param name="desc"/>
<xsl:param name="mode"/>	
<!-- default mode: output pretty linked list; form mode: select box -->

<xsl:output method="html"/>  

<xsl:template match="/"> 

  <xsl:choose>
   <xsl:when test="$mode = 'form'">
      <xsl:apply-templates select="//interpGrp" mode="form"/>
   </xsl:when> 
   <xsl:otherwise>
      <xsl:element name="script">
        <xsl:attribute name="language">Javascript</xsl:attribute>
        <xsl:attribute name="src">toggle-list.js</xsl:attribute>
      </xsl:element> <!-- script -->

      <h3>Categories</h3>
      <xsl:apply-templates select="//interpGrp" />
   </xsl:otherwise>
  </xsl:choose>

</xsl:template> 


<xsl:template match="interpGrp">

<h4>
   <!-- create toggle image -->
   <xsl:element name="a">
<!--      <xsl:attribute name="onclick">toggle_ul('list<xsl:value-of select="$num"/>')</xsl:attribute> -->
     <xsl:element name="img">
       <xsl:attribute
name="onclick">javascript:toggle_ul('<xsl:value-of
select="@type"/>')</xsl:attribute>
       <xsl:attribute name="href">javascript:toggle_ul('<xsl:value-of select="@type"/>')</xsl:attribute>
       <xsl:attribute name="src">images/closed.gif</xsl:attribute>
       <xsl:attribute name="id"><xsl:value-of select="concat(@type,'-gif')"/></xsl:attribute>
     </xsl:element> <!-- img -->
   </xsl:element> <!-- a -->

  <xsl:value-of select="@type"/>
</h4>

  <ul>
    <xsl:attribute name="id"><xsl:value-of select="@type"/></xsl:attribute>
    <xsl:apply-templates select="interp"/>
  </ul>
</xsl:template>

<xsl:template match="interp">
  <li>
   <xsl:element name="a">
     <xsl:attribute name="href">postcards/browse.php?cat=<xsl:value-of
select="@id"/>&amp;desc=<xsl:value-of select="$desc"/></xsl:attribute>
     <xsl:value-of select="@value"/>
   </xsl:element>
  </li>
</xsl:template>

<xsl:template match="interpGrp" mode="form">
  <!-- translate any spaces to dashes for easier passing as http variables -->
  <xsl:variable name="space"> </xsl:variable>
  <xsl:variable name="dash">-</xsl:variable>
  <tr><th><xsl:value-of select="@type"/></th>
  <td>
  <select>
    <xsl:attribute name="name"><xsl:value-of select="translate(@type,$space,$dash)"/></xsl:attribute>
    <option selected="true" value="null">--</option>
    <xsl:apply-templates mode="form"/>
  </select>
  </td></tr>
</xsl:template>

<xsl:template match="interp" mode="form">
  <option>
   <xsl:attribute name="value"><xsl:value-of select="@id"/></xsl:attribute>
    <xsl:value-of select="@value"/>
  </option>
</xsl:template>


</xsl:stylesheet>
