<?xml version="1.0" encoding="ISO-8859-1"?>  

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
	xmlns:html="http://www.w3.org/TR/REC-html40" 
	xmlns:ino="http://namespaces.softwareag.com/tamino/response2" 
	xmlns:xql="http://metalab.unc.edu/xql/">

<!-- Note: all links in this stylesheet should be relative to the root
     directory of the entire site. -->

<xsl:include href="teipoetry.xsl"/>
<xsl:include href="teinote.xsl"/>

<xsl:param name="mode"></xsl:param>
<!-- options:
     browse = list of titles with author/editor
     contents = list of poems (and any other content) in a single book 
     poem = full-text of a single poem (or poem-level item)
     frontmatter = forewords and such
     search = search results (list of poems)
-->

<xsl:output method="html"/>  

<xsl:template match="/"> 
  <xsl:choose>
    <xsl:when test="$mode='browse'">
      <ul>
        <xsl:apply-templates select="//div" mode="browse"/>
     </ul>
    </xsl:when>
    <xsl:when test="$mode='contents'">
      <xsl:apply-templates select="//div" mode="contents"/>
    </xsl:when>
    <xsl:when test="$mode='poem'">
      <xsl:apply-templates select="//div" mode="poem"/>
    </xsl:when>
    <xsl:when test="$mode='frontmatter'">
      <xsl:apply-templates select="//div1"/>
    </xsl:when>
    <xsl:when test="$mode='search'">
      <xsl:apply-templates select="//div2" mode="search"/>
    </xsl:when>
  </xsl:choose>
</xsl:template>



<xsl:template match="div" mode="browse">
 <li>
   <a>
   <xsl:attribute name="href">poetry/contents.php?id=<xsl:value-of select="@id"/></xsl:attribute>
     <xsl:value-of select="titleStmt/title"/></a>
       <br/><xsl:value-of select="titleStmt/author"/>
 </li>
</xsl:template>

<xsl:template match="div" mode="contents">
  <ul>
    <xsl:apply-templates select="div1" mode="contents"/>
  </ul>
  <xsl:apply-templates select="teiHeader"/>
</xsl:template>

<xsl:template match="teiHeader">
<!-- first two are redundant -->
<!--  <xsl:value-of select="fileDesc/titleStmt/title"/><br/>
  <xsl:value-of select="fileDesc/titleStmt/author"/><br/> -->
 <p class="copyright">
  <xsl:value-of select="fileDesc/sourceDesc/bibl"/><br/>
 </p>
</xsl:template>

<xsl:template match="div1" mode="contents">
  <li>
  <xsl:value-of select="@n"/>
  <xsl:if test="not(@n)">
    <xsl:value-of select="head"/>
  </xsl:if>
   <font class="type">(<xsl:value-of select="@type"/>)</font>
	<br/> 
  <xsl:value-of select="docAuthor"/> <xsl:value-of select="docDate"/> <xsl:value-of select="bibl"/>
   
  <ul>
    <xsl:apply-templates select="div2" mode="contents"/>
  </ul>
</li>
</xsl:template>

<!-- similar to above, but with a link to content -->
<xsl:template match="div1[@type='Foreword']" mode="contents">
 <li>
  <a>
   <xsl:attribute name="href">poetry/front.php?id=<xsl:value-of select="@id"/></xsl:attribute>
  <xsl:value-of select="@n"/>
  <xsl:choose>
    <xsl:when test="not(@n)">
      <xsl:value-of select="head"/>
    </xsl:when>
    <xsl:when test="not(head)">
       [untitled]
    </xsl:when>
  </xsl:choose>
  </a>
   <font class="type">(<xsl:value-of select="@type"/>)</font>
 </li>
</xsl:template>

<!-- same as above, but linked to content -->
<xsl:template match="div1[@type='colophon']" mode="contents">
  <li>
    <xsl:value-of select="p"/>    
    <font class="type">(<xsl:value-of select="@type"/>)</font> 
  </li>
</xsl:template>



<xsl:template match="div2" mode="contents">
  <li>
    <a>
     <xsl:attribute name="href">poetry/view.php?id=<xsl:value-of select="@id"/></xsl:attribute>
     <xsl:value-of select="@n"/>
     <xsl:if test="not(@n)">[untitled]</xsl:if>
    </a> 
    <xsl:if test="byline"> - <xsl:call-template name="fix-author">
	<xsl:with-param name="author"><xsl:value-of select="byline"/></xsl:with-param>
	</xsl:call-template>
    </xsl:if>
    <font class="type">(<xsl:value-of select="@type"/>)</font>
  </li>
</xsl:template>

<!-- search results -->
<xsl:template match="div2" mode="search">
   <p>
    <a>
     <xsl:attribute name="href">poetry/view.php?id=<xsl:value-of select="@id"/></xsl:attribute>
     <xsl:value-of select="@n"/>
     <xsl:if test="not(@n)">[untitled]</xsl:if>
    </a> 
    <xsl:if test="byline"> - <xsl:call-template name="fix-author">
	<xsl:with-param name="author"><xsl:value-of select="byline"/></xsl:with-param>
	</xsl:call-template>
    </xsl:if>
   <xsl:if test="docAuthor"> - <xsl:value-of select="docAuthor"/></xsl:if>
      <xsl:apply-templates select="linecount" mode="search"/>
    <p class="linematch">
      <xsl:apply-templates select="l" mode="search"/>
    </p>
  </p>
</xsl:template>

<xsl:template match="linecount" mode="search">
  <font class="extent">
     <xsl:text> (</xsl:text><xsl:value-of select="."/><xsl:text> lines)</xsl:text>
  </font>
</xsl:template>

<!-- matching lines from keyword search -->
<xsl:template match="l" mode="search">
  <xsl:text>... </xsl:text>
  <xsl:apply-templates/>
  <xsl:text> ...</xsl:text>
  <br/>
</xsl:template>

<xsl:template name="fix-author">
 <xsl:param name="author"/>

<!-- first pass: remove comma and following description of author -->
 <xsl:variable name="auth1">
    <xsl:choose>
     <xsl:when test="contains($author, ',')">
       <xsl:value-of select="concat(substring-before($author, ','), '.')"/>
	<!-- note: all others end with final period... -->
     </xsl:when>
     <xsl:otherwise>
       <xsl:value-of select="$author"/>
     </xsl:otherwise>
    </xsl:choose>
  </xsl:variable>

<!-- next: fix case of letters -->
 <xsl:call-template name="convertpropercase">
   <xsl:with-param name="toconvert"><xsl:value-of select="normalize-space($auth1)"/></xsl:with-param>
  </xsl:call-template>

</xsl:template>

<xsl:template match="div1">
  <xsl:apply-templates/>
</xsl:template>

<xsl:template match="p">
  <p><xsl:apply-templates/></p>
</xsl:template>

<xsl:template match="div" mode="poem">

  <xsl:apply-templates select="div2" mode="poem"/>

  <p class="source"> from 
  <a>
   <xsl:attribute name="href">poetry/contents.php?id=<xsl:value-of select="@id"/></xsl:attribute>
     <xsl:value-of select="//titleStmt/title"/></a>, <xsl:value-of select="//titleStmt/author"/>
  </p>

  <xsl:apply-templates select="teiHeader"/>
</xsl:template>

<xsl:template match="div2" mode="poem">
 <table class="poem">
  <tr><td>
   <xsl:apply-templates/>
  </td></tr>
 </table>

  <xsl:call-template name="endnotes"/>
</xsl:template>

<!-- templates to convert case -->
<xsl:template name="convertcase">
  <xsl:param name="toconvert"/>
  <xsl:param name="conversion"/>  <!-- upper/lower -->
<xsl:variable name="lcletters">abcdefghijklmnopqrstuvwxyz</xsl:variable>
<xsl:variable name="ucletters">ABCDEFGHIJKLMNOPQRSTUVWXYZ</xsl:variable>

<xsl:choose>
  <xsl:when test="$conversion='upper'">
    <xsl:value-of select="translate($toconvert,$lcletters,$ucletters)"/>
  </xsl:when>
  <xsl:when test="$conversion='lower'">
    <xsl:value-of select="translate($toconvert,$ucletters,$lcletters)"/>
  </xsl:when>
</xsl:choose>

</xsl:template>


<xsl:template name='convertpropercase'>
<xsl:param name='toconvert' />

<xsl:if test="string-length($toconvert) > 0">
	<xsl:variable name='f' select='substring($toconvert, 1, 1)' />
	<xsl:variable name='s' select='substring($toconvert, 2)' />
	
	<xsl:call-template name='convertcase'>
	  <xsl:with-param name='toconvert' select='$f' />
	  <xsl:with-param name='conversion'>upper</xsl:with-param>
	</xsl:call-template>

<xsl:choose>
	<xsl:when test="contains($s,' ')">
         <xsl:call-template name="convertcase">
            <xsl:with-param name="toconvert" select='substring-before($s," ")'/>
            <xsl:with-param name="conversion">lower</xsl:with-param>
         </xsl:call-template>
		<xsl:text> </xsl:text>
		<xsl:call-template name='convertpropercase'>
		<xsl:with-param name='toconvert' select='substring-after($s," ")' />
		</xsl:call-template>
	</xsl:when>
<!-- special case: initials without spaces -->
        <xsl:when test="contains($s, '.')">
         <xsl:call-template name="convertcase">
            <xsl:with-param name="toconvert" select='substring-before($s,".")'/>
            <xsl:with-param name="conversion">lower</xsl:with-param>
         </xsl:call-template>
		<xsl:text>.</xsl:text>
		<xsl:call-template name='convertpropercase'>
		<xsl:with-param name='toconvert' select='substring-after($s,".")' />
		</xsl:call-template>
        </xsl:when>
	<xsl:otherwise>
         <xsl:call-template name="convertcase">
	    <xsl:with-param name="toconvert"><xsl:value-of select='$s'/></xsl:with-param>
            <xsl:with-param name="conversion">lower</xsl:with-param>
         </xsl:call-template>
	</xsl:otherwise>
</xsl:choose>
</xsl:if>
</xsl:template>


</xsl:stylesheet>
