<?xml version="1.0" encoding="ISO-8859-1"?>  

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
	xmlns:html="http://www.w3.org/TR/REC-html40" 
	xmlns:ino="http://namespaces.softwareag.com/tamino/response2" 
	xmlns:xql="http://metalab.unc.edu/xql/">

<!-- handle footnotes -->


<!-- note, default mode : calculate note number & link to actual note -->
<xsl:template match="note"> 
  <xsl:variable name="pos"><xsl:value-of select="count(preceding::note|ancestor-or-self::note)"/></xsl:variable>
   <xsl:element name="a">
    <xsl:attribute name="name"><xsl:value-of select="concat('notelink',$pos)"/></xsl:attribute>
    <xsl:attribute name="href"><xsl:value-of select="concat('#note',$pos)"/></xsl:attribute>
    <xsl:attribute name="class">footnote</xsl:attribute>
      <xsl:value-of select="$pos"/>
   </xsl:element>
</xsl:template>


<!-- generate text of actual notes -->
<!-- Note: this template MUST be explicitly called, after the text is
     processed -->
<xsl:template name="endnotes">
<!-- only display endnote div if there actually are notes -->
  <xsl:if test="count(//note) > 0">
    <div class="endnote">
      <h2>Notes</h2>
      <xsl:apply-templates select="//note" mode="end"/>
    </div>
 </xsl:if>    
</xsl:template>

<!-- note, endnote mode : calculate number and display content of
     note; link back to note in the text -->
<xsl:template match="note" mode="end">
  <xsl:variable name="pos"><xsl:value-of select="position()"/></xsl:variable>

  <xsl:element name="p">
    <xsl:attribute name="class">footnote</xsl:attribute>
    <xsl:element name="a">
      <xsl:attribute name="name"><xsl:value-of select="concat('note',$pos)"/></xsl:attribute>
      <xsl:attribute name="href"><xsl:value-of select="concat('#notelink',$pos)"/></xsl:attribute>
      <xsl:attribute name="title">Return to text</xsl:attribute>
        <xsl:value-of select="$pos"/> 
    </xsl:element>. <!-- a -->
 
  <xsl:apply-templates mode="endnote"/>

  <!-- special case: properly display poetry within a note -->
  <xsl:if test="count(.//l) > 0">
   <table border="0">
      <tr><td>
        <xsl:apply-templates select="l"/>
      </td></tr>
    </table>
  </xsl:if>    
  </xsl:element> <!-- p -->

</xsl:template>

<xsl:template match="note/p" mode="endnote">
   <xsl:apply-templates/><br/>
</xsl:template>

<!-- handle poetry lines within a note separately -->
<xsl:template match="note/l" mode="endnote">
</xsl:template>


</xsl:stylesheet>