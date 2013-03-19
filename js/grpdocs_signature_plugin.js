(function() {
	tinymce.PluginManager.requireLangPack('grpdocs_signature');
	tinymce.create('tinymce.plugins.GrpdocsSignaturePlugin', {
		init : function(ed,url) {
			ed.addCommand('mceGrpdocsSignature', function() {
				ed.windowManager.open( {
					file : url + '/../grpdocs-dialog.php',
					width : 420 + parseInt(ed.getLang('grpdocs_signature.delta_width',0)),
					height : 540 + parseInt(ed.getLang('grpdocs_signature.delta_height',0)),
					inline : 1}, {
						plugin_url : url,
						some_custom_arg : 'custom arg'
					}
				)}
			);
			ed.addButton('grpdocs_signature', {
				title : 'GroupDocs Signature Embedder',
				cmd : 'mceGrpdocsSignature',
				image : url + '/../images/grpdocs-signature-button.png'
			});
			ed.onNodeChange.add
				(function(ed,cm,n) {
					cm.setActive('grpdocs_signature',n.nodeName=='IMG')
				})
		},
		createControl : function(n,cm) {
			return null
		},
		getInfo : function() { 
			return { 
				longname : 'GroupDocs Signature Embedder',
				author : 'Sergiy Osypov',
				authorurl : 'http://www.groupdocs.com',
				infourl : 'http://www.groupdocs.com',
				version : "1.0"}
		}
	});
	tinymce.PluginManager.add('grpdocs_signature',tinymce.plugins.GrpdocsSignaturePlugin)
})();
