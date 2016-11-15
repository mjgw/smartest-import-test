  <meta name="generator" content="Smartest" />
  
<?sm:if $this.page.type = 'ITEMCLASS' && $this.principal_item:?>
  <meta property="og:title" content="<?sm:$this.piece.social_media_headline:?>" />
  <?sm:if $this.principal_item._thumbnail.id:?><meta property="og:image" content="<?sm:$this.principal_item._thumbnail.image.absolute_web_path:?>" /><?sm:/if:?>
  <meta property="og:description" content="<?sm:$this.principal_item._description:?>" />
  <meta name="twitter:card" content="summary" />
  <meta name="twitter:title" content="<?sm:$this.principal_item.name:?>" />
  <meta name="twitter:description" content="<?sm:$this.principal_item._description:?>" />
  <?sm:if $this.principal_item._thumbnail.id:?><meta name="twitter:image" content="<?sm:$this.principal_item._thumbnail.image.absolute_web_path:?>" /><?sm:/if:?>
<?sm:else:?>
  <meta property="og:title" content="<?sm:$this.page.formatted_title:?>" />
  <meta property="og:description" content="<?sm:$this.page.meta_description:?>" />
<?sm:/if:?>