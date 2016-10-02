<?php
/*=================================================
   charset=utf-8
   Project:	phpGedView
   File:	lang.pt-br.php
   Author:	John Finlay
   Translator:	Maurício Menegazzo Rosa
   Comments:	Brasilian Portuguese Language file for PhpGedView
   Change Log:	See LANG_CHANGELOG.txt
   2005.02.19 "PhpGedView" and "GEDCOM" made consistent across all language files  G.Kroll (canajun2eh)
===================================================*/
# $Id: lang.pt-br.php,v 1.1 2005/10/07 18:08:36 skenow Exp $
if (preg_match("/lang\..+\.php$/", $_SERVER["PHP_SELF"])>0) {
    print "You cannot access a language file directly.";
    exit;
}
//-- GENERAL HELP MESSAGES
$pgv_lang["qm"]			= "?";
$pgv_lang["qm_ah"]				= "?";
$pgv_lang["page_help"]          = "Ajuda";
$pgv_lang["help_for_this_page"]		= "Ajuda desta página";
$pgv_lang["help_contents"]			= "Ajuda Índice";
$pgv_lang["show_context_help"]		= "Mostra Ajuda ao Contexto";
$pgv_lang["hide_context_help"]		= "Não Mostra Ajuda ao Contexto";
$pgv_lang["sorry"]				= "<b>Desculpe, nós ainda não completamos o texto de ajuda para esta página ou ítem </b>";
$pgv_lang["help_not_exist"]			= "<b>Um texto de Ajuda, para esta página ou ítem, ainda não está disponível</b>";
$pgv_lang["resolution"]				= "Resolução de tela";
$pgv_lang["menu"]					= "Menu";
$pgv_lang["header"]				= "Cabeçalho";
$pgv_lang["imageview"]				= "Visualizador de Imagens";

//-- CONFIG FILE MESSAGES
$pgv_lang["login_head"]				= "PhpGedView Indentificação de Usuário";
$pgv_lang["error_title"]		= "ERRO: Não pude abrir o Arquivo GEDCOM";
$pgv_lang["error_header"] 		= "O arquivo GEDCOM, [#GEDCOM#], não existe no local informado.";
$pgv_lang["error_header_write"]		= "O arquivo GEDCOM, [#GEDCOM#], não tem permissão para escrita. Verifique atributos e direitos de acesso.";
$pgv_lang["for_support"]			= "Para Suporte Técnico e Informações entre em contato com";
$pgv_lang["for_contact"]			= "Para Ajuda sobre questões Genealógicas entre em contato com";
$pgv_lang["for_all_contact"]			= "Para suporte técnico ou questões Genealógicas, por favor entre em contato com";
$pgv_lang["build_title"]		= "Gerando Arquivo de Índices";
$pgv_lang["build_error"]			= "O Arquivo GEDCOM foi atualizado.";
$pgv_lang["please_wait"]			= "Por Favor aguarde enquanto os arquivos de índices são regerados.";
$pgv_lang["choose_gedcom"]		= "Escolha o arquivo GEDCOM";
$pgv_lang["username"]				= "Usuário";
$pgv_lang["invalid_username"]			= "Nome do Usuário contém caracteres inválidos";
$pgv_lang["fullname"]				= "Nome Completo";
$pgv_lang["password"]				= "Senha";
$pgv_lang["confirm"]				= "Confirme Senha";
$pgv_lang["user_contact_method"]		= "Método preferido de Contato";
$pgv_lang["login"]				= "Conectar";
$pgv_lang["login_aut"]				= "Alterar Usuário";
$pgv_lang["logout"]				= "Desconectar";
$pgv_lang["admin"]				= "Administrador";
$pgv_lang["logged_in_as"]		= "Conectado como ";
$pgv_lang["my_pedigree"]		= "Minha Árvore";
$pgv_lang["my_indi"]				= "Meus Dados";
$pgv_lang["yes"]				= "Sim";
$pgv_lang["no"]					= "Não";
$pgv_lang["add_gedcom"]				= "Incluir arquivo GEDCOM";
$pgv_lang["no_support"]				= "Nós descobrimos que o seu Browser não suporta os requisitos necessários a este programa. Muitos Browsers suportam estes requisitos em suas versões mais novas. Por Favor faça uma atualização de seu Browser.";
$pgv_lang["change_theme"]			= "Temas";

//-- INDEX (PEDIGREE_TREE) FILE MESSAGES
$pgv_lang["index_header"]	= "Árvore Genealógica";
$pgv_lang["gen_ped_chart"]			= "#PEDIGREE_GENERATIONS# Gerações";
$pgv_lang["generations"]	= "Gerações";
$pgv_lang["view"]			= "Ver";
$pgv_lang["fam_spouse"]		= "Família com cônjuge:";
$pgv_lang["root_person"]			= "ID Raiz";
$pgv_lang["hide_details"]	= "Esconder Detalhes";
$pgv_lang["show_details"]	= "Mostrar Detalhes";
$pgv_lang["person_links"]			= "Links para gráficos, famílias e parentes próximos desta pessoa. Clique neste ícone para ver a página começando nesta pessoa.";
$pgv_lang["zoom_box"]				= "Zoom +/- nesta caixa";
$pgv_lang["portrait"]       = "Retrato";
$pgv_lang["landscape"]      = "Paisagem";
$pgv_lang["start_at_parents"]			= "Iniciar nos Pais";
$pgv_lang["charts"]				= "Gráficos";
$pgv_lang["lists"]				= "Listas";
$pgv_lang["welcome_page"]			= "Página Inicial";
$pgv_lang["max_generation"]			= "O número máximo de gerações é #PEDIGREE_GENERATIONS#.";
$pgv_lang["min_generation"]			= "O número mínimo de gerações é 3.";
$pgv_lang["box_width"] 				= "Largura";

//-- FUNCTIONS FILE MESSAGES
$pgv_lang["unable_to_find_family"]		= "Incapaz de localizar família com este ID";
$pgv_lang["unable_to_find_indi"]		= "Incapaz de localizar pessoa com este ID";
$pgv_lang["unable_to_find_record"]		= "Incapaz de localizar registro com este ID";
$pgv_lang["unable_to_find_source"]		= "Incapaz de localizar fonte com este ID";
$pgv_lang["unable_to_find_repo"]		= "Incapaz de localizar um Repositório com este id";
$pgv_lang["repo_name"]			= "Nome do Repositório:";
$pgv_lang["address"]			= "Endereço:";
$pgv_lang["phone"]				= "Telefone:";
$pgv_lang["source_name"]		= "Nome da Fonte:";
$pgv_lang["title"]				= "Título";
$pgv_lang["author"]				= "Autor:";
$pgv_lang["publication"]		= "Publicação:";
$pgv_lang["call_number"]		= "Número da Chamada:";
$pgv_lang["living"]				= "Vivo";
$pgv_lang["private"]			= "Privado";
$pgv_lang["birth"]				= "Nascimento:";
$pgv_lang["death"]				= "Falecimento:";
$pgv_lang["descend_chart"]		= "Descendentes";
$pgv_lang["individual_list"]	= "Pessoas";
$pgv_lang["family_list"]		= "Famílias";
$pgv_lang["source_list"]		= "Fontes";
$pgv_lang["place_list"]			= "Locais";
$pgv_lang["place_list_aft"] 			= "Locais após";
$pgv_lang["media_list"]				= "Objetos Multimídia";
$pgv_lang["search"]				= "Pesquisar";
$pgv_lang["clippings_cart"]		= "Extração de Dados";
$pgv_lang["not_an_array"]		= "Não é um Array";
$pgv_lang["print_preview"]			= "Exibir formato de Impressão";
$pgv_lang["cancel_preview"]			= "Voltar ao modo normal de exibição";
$pgv_lang["change_lang"]		= "Trocar o Idioma";
$pgv_lang["print"]				= "Imprimir";
$pgv_lang["total_queries"]			= "Nº de consultas ao Banco de Dados: ";
$pgv_lang["total_privacy_checks"]		= "Quantidade de verificações de privacidade:";
$pgv_lang["back"]				= "Voltar";
$pgv_lang["privacy_list_indi_error"]	= "De acordo com as regras de privacidade, estarão ocultas informações de uma ou mais pessoas.";
$pgv_lang["privacy_list_fam_error"]		= "De acordo com as regras de privacidade, estarão ocultas informações de uma ou mais famílias.";

//-- INDIVUDUAL FILE MESSAGES
$pgv_lang["aka"]					= "AKA('s) Também conhecido(s) como";
$pgv_lang["male"]				= "Masculino";
$pgv_lang["female"]				= "Feminino";
$pgv_lang["temple"]				= "Templo LDS";
$pgv_lang["temple_code"]			= "Código Templo LDS:";
$pgv_lang["status"]				= "Status";
$pgv_lang["source"]				= "Fonte";
$pgv_lang["citation"]			= "Citação:";
$pgv_lang["text"]				= "Texto da Fonte:";
$pgv_lang["note"]					= "Nota";
$pgv_lang["NN"]					= "(Nome desconhecido)";
$pgv_lang["PN"]					= "(Prenome desconhecido)";
$pgv_lang["unrecognized_code"]	= "Código GEDCOM Desconhecido";
$pgv_lang["unrecognized_code_msg"]		= "Isto é um erro, e nós gostaríamos de corrigí-lo. Por favor, informe esse erro para";
$pgv_lang["indi_info"]				= "Informação Pessoal";
$pgv_lang["pedigree_chart"]		= "Árvore Genealógica";
$pgv_lang["desc_chart2"]		= "Descendentes";
$pgv_lang["family"]				= "Família";
$pgv_lang["family_with"]			= "Família com";
$pgv_lang["as_spouse"]			= "Família com Cônjuge";
$pgv_lang["as_child"]			= "Família com Pais";
$pgv_lang["view_gedcom"]		= "Ver registro GEDCOM";
$pgv_lang["add_to_cart"]		= "Adicionar ao Carrinho de Recortes";
$pgv_lang["still_living_error"]		= "Esta pessoa ainda está viva ou não tem data de aniversário ou falecimento registrada. Todos os detalhes de pessoas vivas são privados para o público<br />Para maiores informações entre em contato com";
$pgv_lang["privacy_error"]		   	= "Os detalhes desta pessoa são privados.<br />";
$pgv_lang["more_information"]			= "Para maiores informações entre em contato com";
$pgv_lang["name"]					= "Nome";
$pgv_lang["given_name"]			= "Prenome:";
$pgv_lang["surname"]			= "Sobrenome:";
$pgv_lang["suffix"]				= "Sufixo:";
$pgv_lang["object_note"]		= "Notas sobre o Objeto:";
$pgv_lang["sex"]					= "Sexo";
$pgv_lang["personal_facts"]		= "Dados Pessoais e Detalhes";
$pgv_lang["type"]				= "Tipo";
$pgv_lang["date"]				= "Data";
$pgv_lang["place_description"]	= "Local / Descrição";
$pgv_lang["parents"] 			= "Pais:";
$pgv_lang["siblings"] 			= "Irmãos";
$pgv_lang["father"] 			= "Pai";
$pgv_lang["mother"] 			= "Mãe";
$pgv_lang["relatives"]			= "Parentes Próximos";
$pgv_lang["child"]				= "Filho";
$pgv_lang["spouse"]				= "Cônjuge";
$pgv_lang["surnames"]			= "Sobrenomes";
$pgv_lang["adopted"]			= "Adotado";
$pgv_lang["foster"]				= "Adotivo";
$pgv_lang["sealing"]			= "Sealing";
$pgv_lang["link_as"]			= "Ligue esta pessoa a uma família existente como um ";
$pgv_lang["no_tab1"]				= "Não há nenhum Fato para essa pessoa.";
$pgv_lang["no_tab2"]				= "Não há nenhuma Nota para essa pessoa.";
$pgv_lang["no_tab3"]				= "Não há nenhuma Fonte para essa pessoa.";
$pgv_lang["no_tab4"]				= "Não há nenhum Objeto Multimídia para essa pessoa.";
$pgv_lang["no_tab5"]				= "Não há nenhum Parente Próximo para essa pessoa.";

//-- FAMILY FILE MESSAGES
$pgv_lang["family_info"]		= "Informações da Família";
$pgv_lang["family_group_info"]	= "Informações do Grupo Familiar";
$pgv_lang["husband"]			= "Marido";
$pgv_lang["wife"]				= "Esposa";
$pgv_lang["marriage"]			= "Casamento:";
$pgv_lang["lds_sealing"]		= "LDS Sealing:";
$pgv_lang["marriage_license"]			= "Licença Matrimonial:";
$pgv_lang["media_object"]		= "Objeto Multimídia:";
$pgv_lang["children"]			= "Filhos";
$pgv_lang["no_children"]		= "Sem Filhos";
$pgv_lang["parents_timeline"]			= "Mostrar casal no<br />gráfico Linha do Tempo";

//-- CLIPPINGS FILE MESSAGES
$pgv_lang["clip_cart"]					= "Carrinho de Recortes";
$pgv_lang["clip_explaination"]		= "A Extração de Dados permite a você extrair pedaços desta árvore genealógica e formar um novo arquivo GEDCOM para download.<br /><br />";
$pgv_lang["item_with_id"]			= "Item com id";
$pgv_lang["error_already"]			= "já está no seu Carrinho de Recortes.";
$pgv_lang["which_links"]				= "Quais os links desta família que você gostaria de adicionar?";
$pgv_lang["just_family"]			= "Adicione somente este registro desta família.";
$pgv_lang["parents_and_family"]		= "Adicione os registros dos pais desta família.";
$pgv_lang["parents_and_child"]		= "Adicione os registros dos pais e filhos desta família.";
$pgv_lang["parents_desc"]			= "Adicione os registros dos pais e todos descendentes desta família.";
$pgv_lang["continue"]					= "Continue Adicionando";
$pgv_lang["which_p_links"]			= "Quais links desta pessoa você deseja adicionar?";
$pgv_lang["just_person"]				= "Somente esta pessoa.";
$pgv_lang["person_parents_sibs"]	= "Adiciona esta pessoa, seus pais e irmãos.";
$pgv_lang["person_ancestors"]		= "Adiciona esta pessoa e sua linha direta de ancestrais.";
$pgv_lang["person_ancestor_fams"]		= "Adiciona esta pessoa, sua linha direta de ancestrais e suas famílias.";
$pgv_lang["person_spouse"]			= "Adiciona esta pessoa, seu cônjuge e filhos.";
$pgv_lang["person_desc"]				= "Adiciona esta pessoa, seu cônjuge e todos os registros de descendentes.";
$pgv_lang["unable_to_open"]		= "Incapaz de abrir a pasta de recortes para gravação";
$pgv_lang["person_living"]			= "Esta pessoa está viva. Detalhes pessoais não serão incluidos.";
$pgv_lang["person_private"]			= "Detalhes sobre esta pessoa são privados. Detalhes pessoais não serão incluidos.";
$pgv_lang["family_private"]		= "Detalhes sobre esta Família são privados. Detalhes de Família não serão Incluidos.";
$pgv_lang["download"]				= "Clique com botão direito (control-click em um Mac) nos links abaixo e selecione &quot;Salvar destino como ...&quot; para fazer download dos arquivos.";
$pgv_lang["media_files"]			= "Objetos Multimída referenciados neste GEDCOM";
$pgv_lang["cart_is_empty"]			= "Seu carrinho de recortes está vazio.";
$pgv_lang["id"]								= "ID";
$pgv_lang["name_description"]		= "Nome / Descrição";
$pgv_lang["remove"]				= "Remover";
$pgv_lang["empty_cart"]				= "Esvaziar o carrinho";
$pgv_lang["download_now"]			= "Fazer Download agora";
$pgv_lang["indi_downloaded_from"]		= "Download desta Pessoa feito de:";
$pgv_lang["family_downloaded_from"]		= "Download desta Família feito de:";
$pgv_lang["source_downloaded_from"]		= "Download desta Fonte feito de:";

//-- PLACELIST FILE MESSAGES
$pgv_lang["connections"]			= "Conexões encontradas";
$pgv_lang["top_level"]				= "Nível Superior";
$pgv_lang["form"]					= "Locais são classificados da seguinte forma: ";
$pgv_lang["default_form"]				= "Cidade, Estado, País";
$pgv_lang["default_form_info"]		= "(Padrão)";
$pgv_lang["gedcom_form_info"]			= "(GEDCOM)";
$pgv_lang["unknown"]					= "Desconhecido";
$pgv_lang["individuals"]			= "Pessoas";
$pgv_lang["view_records_in_place"]		= "Ver todos os registros encontrados neste local";
$pgv_lang["place_list2"] 			= "Lista de Locais";
$pgv_lang["show_place_hierarchy"]		= "Mostrar Hierarquia dos Locais";
$pgv_lang["show_place_list"]			= "Mostrar lista de todos os Locais";
$pgv_lang["total_unic_places"]		= "Total Locais";

//-- MEDIALIST FILE MESSAGES
$pgv_lang["multi_title"]			= "Objetos Multimídia";
$pgv_lang["media_found"]			= "Objetos Multimídia encontrados";
$pgv_lang["view_person"]				= "Ver Pessoa";
$pgv_lang["view_family"]				= "Ver Família";
$pgv_lang["view_source"]				= "Ver Fonte";
$pgv_lang["prev"]					= "&lt; Anterior";
$pgv_lang["next"]					= "Próximo &gt;";
$pgv_lang["file_not_found"]				= "Arquivo não encontrado.";
$pgv_lang["medialist_show"] 			= "Mostrar";
$pgv_lang["per_page"]				= "objetos multimídia por página";

//-- SEARCH FILE MESSAGES
$pgv_lang["search_gedcom"]			= "Pesquisar arquivo GEDCOM";
$pgv_lang["enter_terms"]			= "Informe os Argumentos da Pesquisa";
$pgv_lang["soundex_search"]			= "Pesquisa Fonética de Nomes";
$pgv_lang["sources"]				= "Fontes";
$pgv_lang["firstname_search"]			= "Nome";
$pgv_lang["lastname_search"]			= "Sobrenome";
$pgv_lang["search_place"]			= "Local";
$pgv_lang["search_year"]			= "Ano";
$pgv_lang["no_results"]				= "Nenhum resultado encontrado.";
$pgv_lang["invalid_search_input"] 		= "Favor informar um Nome, Sobrenome ou Local \\n\\Opcionalmente informe o Ano";

//-- SOURCELIST FILE MESSAGES
$pgv_lang["sources_found"]			= "Locais encontrados";
$pgv_lang["titles_found"]			= "Títulos";

//-- SOURCE FILE MESSAGES
$pgv_lang["source_info"]				= "Informações da Fonte";
$pgv_lang["other_records"]			= "Outros registros relacionados a esta fonte:";
$pgv_lang["people"]						= "Pessoas";
$pgv_lang["families"]				= "Famílias";
$pgv_lang["total_sources"]			= "Total de Fontes";

//-- BUILDINDEX FILE MESSAGES
$pgv_lang["building_indi"]			= "Gerando Índice de Pessoas e Famílias";
$pgv_lang["building_index"]			= "Gerando Índice de Listas";
$pgv_lang["invalid_gedformat"]		= "Formato de arquivo GEDCOM 5.5 inválido";
$pgv_lang["importing_records"]		= "Importando registros para o Banco do Dados";
$pgv_lang["detected_change"]			= "PhpGedView descobriu uma alteração no arquivo GEDCOM #GEDCOM#. Arquivos de índices devem ser regerados antes de prosseguir.";
$pgv_lang["please_be_patient"]		= "Por Favor, seja paciente !!!";
$pgv_lang["reading_file"]			= "Lendo arquivo GEDCOM";
$pgv_lang["flushing"]				= "Esvaziando conteúdo";
$pgv_lang["found_record"]			= "Registro encontrado";
$pgv_lang["exec_time"]				= "Tempo total de execução";
$pgv_lang["unable_to_create_index"]		= "Impossibilitado de criar arquivo de índice.  Certifique-se que premissões de escrita estejam disponíveis ao PhpGedViewDirectory.  Permissões podem/devem ser restauradas após gravação dos arquivos de índice.";
$pgv_lang["indi_complete"]			= "Geração do índice de Pessoas terminado.";
$pgv_lang["family_complete"]		= "Geração do índice de Famílias terminado.";
$pgv_lang["source_complete"]		= "Geração do índice de Fontes terminado.";
$pgv_lang["tables_exist"]			= "Tabelas PhpGedView já existem no Banco de Dados";
$pgv_lang["you_may"]				= "Você pode:";
$pgv_lang["drop_tables"]			= "Abandonar as tabelas atuais";
$pgv_lang["import_multiple"]			= "Importar e trabalhar com múltiplos arquivos GEDCOM";
$pgv_lang["explain_options"]			= "Se você escolher Abandonar as tabelas todos os dados serão substituidos por este GEDCOM.<br />Se você escolher Importar e trabalhar com múltiplos arquivos GEDCOM, PhpGedView apagará todo dado importado usando o GEDCOM com o mesmo nome.  Esta opção permite você armazenar múltiplas informações GEDCOM na mesma tabela e facilmente alternar entre as mesmas.";
$pgv_lang["path_to_gedcom"]			= "Informe o caminho do seu arquivo GEDCOM:";
$pgv_lang["gedcom_title"]			= "Informe o título que descreve os dados deste arquivo GEDCOM:";
$pgv_lang["dataset_exists"]			= "Um arquivo GEDCOM com este nome já foi importado para esse banco de dados.";
$pgv_lang["empty_dataset"]			= "Você deseja apagar os dados antigos e substituir por estes novos?";
$pgv_lang["index_complete"]			= "Geração de índices terminada.";
$pgv_lang["click_here_to_go_to_pedigree_tree"] = "Clique aqui para ir para a Árvore Genealógica.";
$pgv_lang["updating_is_dead"]		= "Updating is dead status for INDI ";
$pgv_lang["import_complete"]			= "Importação terminada.";
$pgv_lang["updating_family_names"]		= "Atualizando nomes de família para FAM ";
$pgv_lang["processed_for"]			= "Arquivo processado por ";
$pgv_lang["run_tools"]				= "Você deseja executar alguma destas ferramentas no seu GEDCOM antes de importá-lo:";
$pgv_lang["addmedia"]				= "Ferramenta de Adição de Fotos";
$pgv_lang["dateconvert"]			= "Ferramenta de Conversão de Datas";
$pgv_lang["xreftorin"]				= "Converte XREF IDs para números RIN";
$pgv_lang["tools_readme"]			= "Veja a seção de Ferramentas no arquivo &lt;a href=&quot;readme.txt&quot;&gt;readme.txt&lt;/a&gt; para maiores informações.";
$pgv_lang["sec"]					= "segundos.";
$pgv_lang["bytes_read"]				= "Bytes Lidos";
$pgv_lang["created_indis"]			= "Criada com sucesso tabela de <i>Pessoas</i>.";
$pgv_lang["created_indis_fail"]		= "Incapaz de criar tabela de <i>Pessoas</i>.";
$pgv_lang["created_fams"]			= "Criada com sucesso tabela de <i>Famílias</i>.";
$pgv_lang["created_fams_fail"]		= "Incapaz de criar tabela de <i>Famílias</i>.";
$pgv_lang["created_sources"]			= "Criada com sucesso tabela de <i>Fontes</i>.";
$pgv_lang["created_sources_fail"]		= "Incapaz de criar tabela de <i>Fontes</i>.";
$pgv_lang["created_other"]			= "Criada com sucesso <i>Outras</i> tabelas.";
$pgv_lang["created_other_fail"]		= "Incapaz de criar <i>Outras</i> tabelas.";
$pgv_lang["created_places"]			= "Criada com sucesso tabela de <i>Locais</i>.";
$pgv_lang["created_places_fail"]		= "Incapaz de criar tabela de <i>Locais</i>.";
$pgv_lang["import_progress"]			= "Importação em progresso, Aguarde ...";

//-- INDIVIDUAL AND FAMILYLIST FILE MESSAGES
$pgv_lang["total_fams"]				= "Total Famílias";
$pgv_lang["total_indis"]			= "Total Pessoas";
$pgv_lang["starts_with"]			= "Começar Com:";
$pgv_lang["person_list"]			= "Listar Pessoa:";			// ??
$pgv_lang["paste_person"]			= "Colar Pessoa";				// ??
$pgv_lang["notes_sources_media"]		= "Notas, Fontes e Mídia";
$pgv_lang["notes"]				= "Notas";
$pgv_lang["ssourcess"]				= "Fontes";
$pgv_lang["media"]				= "Objetos Multimídia";
$pgv_lang["name_contains"]			= "Nome Contendo:";
$pgv_lang["filter"]				= "Pesquisar";
$pgv_lang["find_individual"]			= "Procurar ID de Pessoa";
$pgv_lang["find_familyid"]			= "Procurar ID de Família";
$pgv_lang["find_sourceid"]			= "Procurar ID de Fonte";
$pgv_lang["skip_surnames"]			= "Mostre Lista de Pessoas";
$pgv_lang["show_surnames"]			= "Mostrar Lista de Sobrenomes";
$pgv_lang["all"]					= "Todos";
$pgv_lang["hidden"]				= "Secreto";
$pgv_lang["confidential"]			= "Confidencial";

//-- TIMELINE FILE MESSAGES
$pgv_lang["age"]					= "Idade";
$pgv_lang["timeline_title"]			= "Gráfico da Linha do Tempo";
$pgv_lang["timeline_chart"]			= "Linha do Tempo";
$pgv_lang["remove_person"]			= "Remover Pessoa";
$pgv_lang["show_age"]				= "Mostrar Marcador de Idade";
$pgv_lang["add_another"]			= "Adicione uma outra pessoa ao gráfico:<br />ID Pessoa:";
$pgv_lang["find_id"]				= "Procurar ID";
$pgv_lang["show"]					= "Mostrar";
$pgv_lang["year"]					= "Ano:";
$pgv_lang["timeline_instructions"]		= ">>> Em Browsers mais novos você clicar e mover as caixas na linha do tempo com o mouse <<<";
$pgv_lang["zoom_in"]				= "Mais Zoom";
$pgv_lang["zoom_out"]				= "Menos Zoom";

//-- MONTH NAMES
$pgv_lang["jan"]			= "Janeiro";
$pgv_lang["feb"]			= "Fevereiro";
$pgv_lang["mar"]			= "Março";
$pgv_lang["apr"]			= "Abril";
$pgv_lang["may"]			= "Maio";
$pgv_lang["jun"]			= "Junho";
$pgv_lang["jul"]			= "Julho";
$pgv_lang["aug"]					= "Agosto";
$pgv_lang["sep"]			= "Setembro";
$pgv_lang["oct"]			= "Outubro";
$pgv_lang["nov"]			= "Novembro";
$pgv_lang["dec"]			= "Dezembro";
$pgv_lang["abt"]			= "sobre";
$pgv_lang["aft"]			= "após";
$pgv_lang["and"]			= "e";
$pgv_lang["bef"]			= "antes";
$pgv_lang["bet"]			= "entre";
$pgv_lang["cal"]			= "calculado";
$pgv_lang["est"]			= "estimado";
$pgv_lang["from"]			= "de";
$pgv_lang["int"]			= "interpretado";
$pgv_lang["to"]				= "para";
$pgv_lang["cir"]					= "aproximadamente";
$pgv_lang["apx"]			= "aprox.";

//-- Admin File Messages
$pgv_lang["select_an_option"]		= "Escolha uma opção abaixo:";
$pgv_lang["readme_documentation"]	= "Documentação";
$pgv_lang["configuration"]			= "Configuração";
$pgv_lang["rebuild_indexes"]			= "Reconstruir Índices";
$pgv_lang["user_admin"]				= "Administração de Usuários";
$pgv_lang["user_created"]			= "Usuário criado com sucesso.";
$pgv_lang["user_create_error"]		= "Incapaz de criar usuário. Por favor volte e tente novamente.";
$pgv_lang["password_mismatch"]		= "Senhas não conferem.";
$pgv_lang["enter_username"]			= "Você deve informar o nome do usuário.";
$pgv_lang["enter_fullname"]			= "Você deve informar o nome completo.";
$pgv_lang["enter_password"]			= "Você deve informar a senha.";
$pgv_lang["confirm_password"]			= "Você deve confirmar a senha.";
$pgv_lang["update_user"]			= "Alterar Conta do Usuário";
$pgv_lang["update_myaccount"]			= "Alterar Minha Conta";
$pgv_lang["save"]					= "Gravar";
$pgv_lang["delete"]				= "Excluir";
$pgv_lang["edit"]					= "Editar";
$pgv_lang["full_name"]				= "Nome Completo";
$pgv_lang["visibleonline"]			= "Visível para outros usuários quando on-line";
$pgv_lang["editaccount"]			= "Permite este usuário alterar as informações de sua conta";
$pgv_lang["admin_gedcom"]			= "Administrar GEDCOM";
$pgv_lang["confirm_user_delete"]		= "Tem certeza que deseja excluir o usuário";
$pgv_lang["create_user"]			= "Criar Usuário";
$pgv_lang["no_login"]				= "Incapaz de autenticar o usuário.";
$pgv_lang["import_gedcom"]			= "Importar este arquivo GEDCOM";
$pgv_lang["duplicate_username"]		= "Nome de Usuário duplicado.  Já existe um usuário com este nome. Por favor escolha outro nome de usuário.";
$pgv_lang["gedcomid"]				= "Minha Identificação na Árvore Genealógica";
$pgv_lang["enter_gedcomid"]			= "Você deve informar um ID.";
$pgv_lang["user_info"]				= "Minhas Informações";
$pgv_lang["rootid"]					= "Pessoa raiz da Árvore Genealógica";
$pgv_lang["download_gedcom"]		= "Download GEDCOM";
$pgv_lang["upload_gedcom"]			= "Upload GEDCOM";
$pgv_lang["add_new_gedcom"]			= "Criar um novo arquivo GEDCOM";
$pgv_lang["gedcom_file"]			= "Arquivo GEDCOM";
$pgv_lang["enter_filename"]			= "Você dever informar o nome do arquivo GEDCOM.";
$pgv_lang["file_not_exists"]			= "O nome do arquivo informado não existe.";
$pgv_lang["file_exists"]			= "Já existe um arquivo GEDCOM com esse nome. Por favor escolha um nome diferente ou exclua o outro arquivo.";
$pgv_lang["new_gedcom_title"]			= "Genealogia de [#GEDCOMFILE#]";
$pgv_lang["upload_error"]			= "Um erro ocorreu quando fazia upload de seu arquivo GEDCOM.";
$pgv_lang["upload_help"]			= "Selecione o arquivo de seu computador para fazer upload ao seu servidor.  O upload de todos os arquivos serão do diretório:";
$pgv_lang["add_gedcom_instructions"]	= "Informe um novo nome para este novo arquivo GEDCOM.  O novo arquivo GEDCOM será criado no diretório Index: ";
$pgv_lang["file_success"]			= "Upload do arquivo executado com sucesso";
$pgv_lang["file_too_big"]			= "Arquivo a ser feito upload excede tamanho permitido";
$pgv_lang["file_partial"]			= "Upload partiacialmente executado, por favor tente novamente";
$pgv_lang["file_missing"]			= "Nenhum arquivo foi recebido. Por favor tente novamente.";
$pgv_lang["manage_gedcoms"]			= "Gerenciar arquivos GEDCOM e editar Privacidade";
$pgv_lang["administration"]			= "Administração";
$pgv_lang["ansi_to_utf8"]			= "Converter esse arquivo GEDCOM do formato ANSI (ISO-8859-1) para UTF-8?";
$pgv_lang["utf8_to_ansi"]			= "Você quer converter este arquivo GEDCOM do formato UTF-8 para ANSI (ISO-8859-1)?";
$pgv_lang["user_manual"]			= "PhpGedView Manual do Usuário";
$pgv_lang["upgrade"]				= "Atualizar PhpGedView/ResearchLog";
$pgv_lang["view_logs"]			= "Ver Logs";
$pgv_lang["logfile_content"]			= "Conteúdo do arquivo de log";
$pgv_lang["step1"]				= "Passo 1 de 4:";
$pgv_lang["step2"]				= "Passo 2 de 4:";
$pgv_lang["step3"]				= "Passo 3 de 4:";
$pgv_lang["step4"]				= "Passo 4 de 4:";
$pgv_lang["validate_gedcom"]			= "Validar GEDCOM";
//////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["img_admin_settings"]		= "Configuração de Edit Image Manipulation";
$pgv_lang["download_note"]			= "ATENÇÃO: Arquivos GEDCOMs muito grandes podem levar muito tempo para iniciar o download.  If PHP times out before the download is complete, then you may not get a complete download.  You can check the downloaded GEDCOM for the 0 TRLR line at the end of the file to make sure it downloaded correctly.  In general it could take as much time to download as it took to import your GEDCOM.";
//////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["pgv_registry"]			= "Ver outros sites usando PhpGedView";
$pgv_lang["verify_upload_instructions"]	= "Se você escolher continuar, o arquivo GEDCOM antigo será substituido pelo arquivo que você fez o upload e o processo de importação iniciará a seguir.  Se você escolher cancelar, o arquivo GEDCOM antigo será mantido inalterado.";
$pgv_lang["cancel_upload"]			= "Cancelar Upload";

//-- Relationship chart messages
$pgv_lang["relationship_chart"]		= "Relacionamentos";
$pgv_lang["person1"]				= "Pessoa 1";
$pgv_lang["person2"]				= "Pessoa 2";
$pgv_lang["no_link_found"]			= "Nenhum (outro) relacionamento entre estas duas pessoas foi localizado.";
$pgv_lang["sibling"]				= "Irmãos";
$pgv_lang["follow_spouse"]			= "Verificar relacionamentos por casamento.";
$pgv_lang["timeout_error"]			= "O tempo destinado à pesquisa encerrou antes de encontrar um relacionamento.";
$pgv_lang["son"]					= "Filho";
$pgv_lang["daughter"]				= "Filha";
$pgv_lang["brother"]				= "Irmão";
$pgv_lang["sister"]					= "Irmã";
$pgv_lang["relationship_to_me"]		= "Relacionamento Comigo";
$pgv_lang["next_path"]				= "Localizar novo caminho ";
$pgv_lang["show_path"]				= "Mostrar caminho";
$pgv_lang["line_up_generations"]		= "Alinhar as mesmas generações";
$pgv_lang["oldest_top"]             	= "Mostrar mais velho no topo";

//-- GEDCOM edit utility
$pgv_lang["check_delete"]			= "Você tem certeza que quer excluir este fato deste arquivo GEDCOM?";
$pgv_lang["access_denied"]		= "<b>Acesso Negado</b><br />Você não tem acesso a este recurso.";
$pgv_lang["gedrec_deleted"]			= "Registro GEDCOM excluido com sucesso.";
$pgv_lang["gedcom_deleted"]			= "GEDCOM [#GED#] excluido com sucesso.";
$pgv_lang["changes_exist"]			= "Foram feitas alterações neste arquivo GEDCOM.";
$pgv_lang["accept_changes"]			= "Aceitar / Rejeitar Alterações";
$pgv_lang["show_changes"]			= "Este registro foi atualizado. Clique aqui para mostrar as alterações.";
$pgv_lang["hide_changes"]			= "Clique aqui para não mostrar as alterações.";
$pgv_lang["review_changes"]			= "Rever Alterações";
$pgv_lang["undo_successful"]			= "Alterações desfeitas com sucesso.";
$pgv_lang["undo"]					= "Desfazer";
$pgv_lang["view_change_diff"]	= "View Change Diff";
$pgv_lang["changes_occurred"]			= "As seguintes mudanças ocorreram para essa pessoa:";
$pgv_lang["find_place"]				= "Procurar Local";
$pgv_lang["close_window"]			= "Fechar Janela";
$pgv_lang["close_window_without_refresh"]	= "Fechar Janela sem Recarregar";
$pgv_lang["place_contains"]			= "Locais contendo:";
//////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["accept_gedcom"]			= "Decide for each change to either accept of reject it.<br />To accept all changes at once, click \"Accept all changes\" in the box below.<br />To get more information about a change, <br />click \"View change diff\" to see the differences between old and new situation, <br />or click \"View GEDCOM record\" to see the new situation in GEDCOM format."; 
//////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["ged_import"]				= "Importar GEDCOM";
$pgv_lang["now_import"]				= "Agora você pode importar os registros GEDCOM para o PhpGedView clicando no link de importação abaixo.";
$pgv_lang["add_fact"]				= "Adicionar novo fato";
$pgv_lang["add"]					= "Adicionar";
$pgv_lang["custom_event"]			= "Configurar Evento";
$pgv_lang["update_successful"]		= "Atualização com sucesso";
$pgv_lang["add_child"]				= "Adicionar filho";
$pgv_lang["add_child_to_family"]		= "Adicionar filho à esta família";
$pgv_lang["add_son_daughter"]			= "Adicionar um filho ou uma filha";
$pgv_lang["add_sibling"]			= "Adicionar um irmão ou irmã";
$pgv_lang["must_provide"]			= "Você deve prover um ";
$pgv_lang["delete_person"]			= "Excluir esta Pessoa";
$pgv_lang["confirm_delete_person"]		= "Você tem certeza que quer excluir esta pessoa do arquivo GEDCOM?";
$pgv_lang["find_media"]				= "Localizar arquivos Multimídia";
$pgv_lang["set_link"]				= "Preparar Link";
$pgv_lang["add_source_lbl"]			= "Adicionar Fonte à Citação";
$pgv_lang["add_source"]				= "Adicionar nova Fonte à Citação";
$pgv_lang["add_note_lbl"]			= "Adicionar Nota";
$pgv_lang["add_note"]				= "Adicionar nova Nota";
$pgv_lang["add_media_lbl"]			= "Adicionar arquivo Multimídia";
$pgv_lang["add_media"]				= "Adicionar novo arquivo Multimídia";
$pgv_lang["delete_source"]			= "Excluir esta Fonte";
$pgv_lang["confirm_delete_source"]		= "Você tem certeza que deseja excluir esta fonte deste arquivo GEDCOM?";
$pgv_lang["add_husb"]				= "Adicionar marido";
$pgv_lang["add_husb_to_family"]		= "Adicionar um marido a esta família";
$pgv_lang["add_wife"]				= "Adicionar esposa";
$pgv_lang["add_wife_to_family"]		= "Adicionar uma esposa a esta família";
$pgv_lang["find_family"]			= "Pesquisar Família";
$pgv_lang["find_fam_list"]			= "Pesquisar lista de Famílias";
$pgv_lang["add_new_wife"]		= "Adiciona uma nova esposa";
$pgv_lang["add_new_husb"]		= "Adiciona um novo marido";
$pgv_lang["edit_name"]				= "Alterar Nome";
$pgv_lang["delete_name"]			= "Excluir Nome";
//////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["no_temple"]			= "No Temple - Living Ordinance";
//////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["replace"]				= "Substituir Registro";
$pgv_lang["append"]				= "Adiconar Registro";
$pgv_lang["add_father"]				= "Adiconar um novo pai";
$pgv_lang["add_mother"]				= "Adiconar uma nova mãe";
$pgv_lang["add_obje"]				= "Adiconar um novo Objeto Multimídia";
$pgv_lang["no_changes"]				= "Não há mudança necessária a ser revisada.";
$pgv_lang["accept"]				= "Aceitar";
$pgv_lang["accept_all"]				= "Aceitar todas as mudanças";
$pgv_lang["accept_successful"]		= "Alterações aceitas com sucesso no banco de dados";
$pgv_lang["edit_raw"]				= "Editar registro raw GEDCOM";
$pgv_lang["select_date"]			= "Selecionar uma data";
$pgv_lang["create_source"]			= "Criar uma nova fonte";
$pgv_lang["new_source_created"]		= "Nova fonte criada com sucesso.";
//////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["paste_id_into_field"]		= "Paste the following source ID into your editing fields to reference this source ";
//////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["add_name"]				= "Adicionar novo Nome";
$pgv_lang["privacy_not_granted"]		= "Você não tem acesso para";
$pgv_lang["user_cannot_edit"]			= "Este usuário não pode editar este GEDCOM.";
$pgv_lang["gedcom_editing_disabled"]	= "Edição deste GEDCOM foi desabilitada pelo administrador do sistema.";
$pgv_lang["privacy_prevented_editing"]	= "As configurações de privacidade impedem você de alterar esse registro.";

//-- calendar.php message";
$pgv_lang["on_this_day"]			= "Este Dia em nossa História ...";
$pgv_lang["in_this_month"]			= "Este Mês em nossa História ...";
$pgv_lang["in_this_year"]			= "Este Ano em nossa História ...";
$pgv_lang["year_anniversary"]			= "#year_var# anos";
$pgv_lang["today"]				= "Hoje";
$pgv_lang["day"]				= "Dia:";
$pgv_lang["month"]				= "Mês:";
$pgv_lang["showcal"]				= "Mostrar Eventos de:";
$pgv_lang["anniversary_calendar"] = "Calendário";
$pgv_lang["sunday"]				= "Domingo";
$pgv_lang["monday"]				= "Segunda";
$pgv_lang["tuesday"]			= "Terça";
$pgv_lang["wednesday"]			= "Quarta";
$pgv_lang["thursday"]			= "Quinta";
$pgv_lang["friday"]				= "Sexta";
$pgv_lang["saturday"]			= "Sábado";
$pgv_lang["viewday"]				= "Ver Dia";
$pgv_lang["viewmonth"]				= "Ver Mês";
$pgv_lang["viewyear"]				= "Ver Ano";
$pgv_lang["all_people"]			= "Todas as Pessoas";
$pgv_lang["living_only"]		= "Pessoas Vivas";
$pgv_lang["recent_events"]		= "Eventos Recentes (< 100 anos)";
$pgv_lang["day_not_set"]			= "Dia não configurado";
$pgv_lang["year_error"]				= "Desculpas, datas anteriores a 1970 não são permitidas.";

//-- upload media messages
$pgv_lang["upload_media"]			= "Upload de arquivos Multimídia";
$pgv_lang["media_file"]				= "Arquivo Multimídia";
$pgv_lang["thumbnail"]			= "Miniatura";
$pgv_lang["upload_successful"]		= "Upload com sucesso";

//-- user self registration module
$pgv_lang["lost_password"]			= "Perdeu sua senha?";
$pgv_lang["requestpassword"]			= "Solicitação de cadastramento de nova senha";
$pgv_lang["no_account_yet"]			= "Ainda não é um usuário cadastrado?";
$pgv_lang["requestaccount"]			= "Solicitação de cadastramento de novo Usuário";
$pgv_lang["register_info_01"]			= "A quantidade de dados publicada e exibida neste website podem ser limitadas devido à aplicação de regras relacionadas à proteção de privacidade. A maioria das pessoas não querem seus dados pessoais publicados e disponíveis na Internet. Estes podem ser usados para Spam ou invasão de privacidade.<br /><br />Para ter acesso aos dados privados, você deve ter uma conta neste website. Para obter uma conta você mesmo pode se registrar fornecendo as informações requeridas. Depois da verificação e aprovação de seu registro pelo Administrador, você estará apto a conectar e visualizar dados privados.<br /><br />Dependendo da configuração você somente terá acesso aos dados de parentes próximos. O Administrador pode também permitir acesso à edição das informações do banco de dados, permitindo inclusive alterações e adições de novas informações.<br /><br />ATENÇÃO: Você somente receberá acesso aos dados privados se você demonstrar que pertence a essa família !!!<br /><br />Se você não tiver parentesco com alguém da família provavelmente não obterá uma conta de acesso.<br />Se você necessitar de alguma outra informação, por favor use o link abaixo para entrar em contato com o webmaster.<br /><br />";
$pgv_lang["register_info_02"]	= "";
$pgv_lang["pls_note01"]				= "Atenção: Neste sistema letras maiúsculas e minúsculas fazem diferença!";
$pgv_lang["min6chars"]				= "Senha deve ter pelo menos 6 caracteres";
$pgv_lang["pls_note02"]				= "Nota: Senhas podem conter números, letras e caracteres especiais.";
$pgv_lang["pls_note03"]				= "Não informe e-mails inexistentes. Este endereço de e-mail será verificado antes da ativação da conta. O mesmo não será exibido no site. Você receberá uma mensagem neste e-mail com seus dados de registro";
$pgv_lang["emailadress"]			= "E-mail";
$pgv_lang["pls_note04"]				= "Campos identificados com * são obrigatórios.";
$pgv_lang["pls_note05"]				= "Uma vez concluido o preenchimento dos dados deste formúlario e verificação de suas respostas, você receberá um e-mail de confirmação no endereço informado acima. Seguindo as instruções do e-mail de confirmação, você ativará sua conta; você terá até 7 dias para confirmar este e-mail. Findo este prazo, você poderá se cadastrar novamente. Para ter acesso a este site você precisa saber seu nome de usuário e senha.<br /><br />";

$pgv_lang["mail01_line01"]			= "Olá #user_fullname# ...";
$pgv_lang["mail01_line02"]			= "Um pedido de cadastramento de conta feito em ( #SERVER_NAME# ) está utilizando seu endereço de E-mail ( #user_email# ).";
$pgv_lang["mail01_line03"]			= "Os seguintes dados estão sendo usuados.";
$pgv_lang["mail01_line04"]			= "Por favor clique no link abaixo e preencha o formulário para validar sua Conta e endereço de E-mail.";
$pgv_lang["mail01_line05"]			= "Se você não solicitou esse cadastramento, favor apagar essa mensagem.";
$pgv_lang["mail01_line06"]			= "Você não receberá nenhum outro E-mail a partir deste sistema, porquê conta sem validação é eliminada automaticamente em sete dias.";
$pgv_lang["mail01_subject"]			= "Seu registro em #SERVER_NAME#";
$pgv_lang["mail02_line01"]			= "Olá Administrador ...";
$pgv_lang["mail02_line02"]			= "Existe um registro de novo usuário em ( #SERVER_NAME# ).";
$pgv_lang["mail02_line03"]			= "O usuário recebeu um e-mail com os dados necessários para confirmar sua conta.";
$pgv_lang["mail02_line04"]			= "Tão logo o usuário faça a validação você será informado por e-mail para dar permissão de acesso ao site.";
$pgv_lang["mail02_subject"]		= "Novo registro em #SERVER_NAME#";

$pgv_lang["hashcode"]				= "Código de validação";
$pgv_lang["thankyou"]				= "Olá #user_fullname# ...<br />Obrigado por seu registro";
$pgv_lang["pls_note06"]				= "Agora você irá receber um e-mail de confirmação no endereço ( #user_email# ). Seguindo as instruções do e-mail de confirmação, você ativará sua conta; você terá até 7 dias para confirmar este e-mail. Para ter acesso a este site você precisa saber seu nome de usuário e senha.";

$pgv_lang["registernew"]			= "Confirmação de nova conta";
$pgv_lang["user_verify"]			= "Validação de Usuário";
$pgv_lang["send"]			= "Enviar";

$pgv_lang["pls_note07"]				= "Por favor, digite nome de Usuário, Senha e Código de validação para receber deste sistema, por E-mail, a validação de seu cadastramento.";
$pgv_lang["pls_note08"]				= "Os dados do Usuário <b>#user_name#</b> foram validados.";

$pgv_lang["mail03_line01"]			= "Olá Administrador ...";
$pgv_lang["mail03_line02"]			= "#newuser[username]# ( #newuser[fullname]# ) verificou os dados de registro.";
$pgv_lang["mail03_line03"]			= "Por favor, clique no link abaixo para conectar ao seu site e verificar os dados do usuário para permitir-lhe acesso ao seu site.";
$pgv_lang["mail03_subject"]			= "Nova verificação em #SERVER_NAME#";

$pgv_lang["pls_note09"]				= "Você foi identificado como um usuário registrado.";
$pgv_lang["pls_note10"]				= "O Administrador será informado.<br />Tão logo ele dê sua permissão você poderá conectar usando seu nome de usuário e senha.";
$pgv_lang["data_incorrect"]			= "Dados incorretos!<br />Por favor tente novamente!";
$pgv_lang["user_not_found"]			= "Não foi possível verificar as informações.  Por favor retorne e tente novamente.";
$pgv_lang["lost_pw_reset"]			= "Solicitação de cadastramento de nova senha";

$pgv_lang["pls_note11"]				= "Para refazer sua senha perdida, forneça os nome de usuário e endereço de e-mail de sua conta. <br /><br />Nós enviaremos via e-mail uma URL especial, a qual contém uma confirmação para sua conta. Visitando a URL fornecida, nós permitiremos a mudança de sua senha e nome de usuário para este site. Por medida de segurança, você não deve fornecer informações desse e-mail para ninguém, incluindo os administradores desse site.<br /><br />Se você precisar de ajuda, por favor, entre em contato com o administrator do site.";
$pgv_lang["enter_email"]		= "Você precisa informar seu e-mail.";

$pgv_lang["mail04_line01"]		= "Oi #user_fullname# ...";
$pgv_lang["mail04_line02"]			= "Uma nova senha foi requerida para o seu usuário!";
$pgv_lang["mail04_line03"]		= "Recomendações:";
$pgv_lang["mail04_line04"]			= "Agora clique no link abaixo, conecte com sua nova senha e altere-a para manter segura a integridade de seus dados.";
$pgv_lang["mail04_subject"]			= "Requisição de Dados de #SERVER_NAME#";

$pgv_lang["pwreqinfo"]				= "Olá ...<br /><br />Um e-mail foi enviado para seu endereço (#user[email]#) incluindo a nova senha.<br /><br />Verifique com seu programa de e-mails o recebimento do mesmo.<br /><br />Recomendação:<br /><br />Após recepção do e-mail com sua solicitação você deve conectar a este site com sua nova senha e alterá-la para manter segura a integridade de seus dados.";

$pgv_lang["editowndata"]		= "Minha Conta";
$pgv_lang["savedata"]			= "Gravar dados alterados";
$pgv_lang["datachanged"]		= "User data was changed!";
$pgv_lang["datachanged_name"]			= "Você precisa reconectar novamente com o seu novo nome de usuário.";
$pgv_lang["myuserdata"]			= "Minha Conta";
$pgv_lang["verified"]				= "Validou sua conta";
$pgv_lang["verified_by_admin"]		= "Aprovado pelo Administrador";
$pgv_lang["user_theme"]			= "Meu Tema";
$pgv_lang["mgv"]					= "Meu Portal";
$pgv_lang["mygedview"]				= "Meu Portal";
$pgv_lang["passwordlength"]		= "Senha precisa conter ao menos 6 caracteres.";
$pgv_lang["admin_approved"]		= "Sua conta em #SERVER_NAME# foi aprovada";
$pgv_lang["you_may_login"]			= " pelo administrador do site. Agora você pode acessar o  Site clicando no link abaixo:";
//////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["welcome_text_auth_mode_1"]	= "<b>WELCOME TO THIS GENEALOGY WEBSITE</b><br /><br />Access to this site is permitted to every visitor who has a user account on this website.<br />If you already have a user account you can login on this page.<br /><br />If you don't have a user account yet, you can apply for one by clicking on the appropriate link on this page.<br />After verifying your information, the site administrator will activate your account.<br />You will receive an email on activation.";
$pgv_lang["welcome_text_auth_mode_2"]	= "<b>WELCOME TO THIS GENEALOGY WEBSITE</b><br /><br />Access to this site is permitted to <b>authorized</b> users only.<br />If you already have a user account you can login on this page.<br /><br />If you don't have a user account yet, you can apply for one by clicking on the appropriate link on this page.<br />After verifying your information, the site administrator will either accept or decline your request.<br/>You will receive an e-mail message upon acceptance of your request.";
$pgv_lang["welcome_text_auth_mode_3"]	= "<b>WELCOME TO THIS GENEALOGY WEBSITE</b><br /><br />Access to this site is permitted <b>to familymembers only</b>.<br />If you already have a user account you can login on this page.<br /><br />If you don't have a user account yet, you can apply for one by clicking on the appropriate link on this page.<br />After verifying your information, the site administrator will either accept or decline your request.<br />You will receive an email when your request is accepted.";
$pgv_lang["welcome_text_cust_head"]		= "<b>WELCOME TO THIS GENEALOGY WEBSITE</b><br /><br />Access is permitted to users who have a useraccount and a password for this website.<br />";
//////////////////////////////////////////////////////////////////////////////////////////

//-- mygedview page
$pgv_lang["welcome"]			= "Bem-Vindo";
$pgv_lang["upcoming_events"]			= "Próximos Eventos";
$pgv_lang["chat"]					= "Bate-papo";
$pgv_lang["users_logged_in"]			= "Usuários Conectados";
$pgv_lang["message"]			= "Enviar Mensagem";
$pgv_lang["my_messages"]		= "Minhas Mensagens";
$pgv_lang["date_created"]			= "Data";
$pgv_lang["message_from"]			= "E-mail";
$pgv_lang["message_from_name"]		= "Seu Nome";
$pgv_lang["message_to"]				= "Para";
$pgv_lang["message_subject"]			= "Assunto";
$pgv_lang["message_body"]			= "Corpo";
$pgv_lang["no_to_user"]				= "Nenhum recipiente de usuário foi fornecido.  Impossível continuar.";
$pgv_lang["provide_email"]			= "Favor fornecer seu endereço de e-mail de forma que possamos responder essa mensagem.  Se você não fornecer seu endereço de e-mail nós não poderemos responder sua mensagem.  Seu endereço de e-mail <b>não</b> será usado para outros fins a não ser para responder sua mensagem.";
$pgv_lang["reply"]				= "Responder";
$pgv_lang["message_deleted"]			= "Mensagem Excluida";
$pgv_lang["message_sent"]		= "Mensagem Enviada";
$pgv_lang["reset"]				= "Restaurar";
$pgv_lang["site_default"]			= "Padrão do Site";
$pgv_lang["mygedview_desc"]			= "A página <b>\"Meu Portal\"</b> permite que você faça 'Bookmarks' de suas pessoas favoritas, veja os próximos eventos e colabore com outros usuários.";
$pgv_lang["no_messages"]		= "Você não tem novas mensagens.";
$pgv_lang["clicking_ok"]			= "Clicando OK, será aberta uma outra janela onde você poderaá contatar ";
$pgv_lang["my_favorites"]		= "Meus Favoritos";
$pgv_lang["no_favorites"]			= "Você não selecionou nenhuma Pessoa. Para adicionar uma Pessoa ao <b>Meus Favoritos</b>, procure os detalhes da Pessoa que você deseja adicionar e clique em \"Adicione aos Meus Favoritos\" ou use a caixa abaixo para adicioná-lo pelo seu número.";
$pgv_lang["add_to_my_favorites"] = "Adicione aos Meus Favoritos";
$pgv_lang["gedcom_favorites"]			= "Favoritos do Site";
$pgv_lang["no_gedcom_favorites"]		= "Até este momento não existe nenhum Favorito selecionado.  O Administrador pode adicionar Favoritos que serão exibidos na inicialização.";
$pgv_lang["confirm_fav_remove"]		= "Tem certeza que deseja remover este item de Meus Favoritos?";
$pgv_lang["invalid_email"]			= "Por favor informe um e-mail válido.";
$pgv_lang["enter_subject"]			= "Por favor informe o Assunto da mensagem.";
$pgv_lang["enter_body"]				= "Por favor, informe algum texto antes de enviar.";
$pgv_lang["confirm_message_delete"]		= "Confirma a exclusão destas mensagens? Isto não poderá ser desfeito.";
$pgv_lang["message_email1"]		= "Esta mensagem foi enviada para você por ";
$pgv_lang["message_email2"]		= "Você enviou esta mensagem para:";
$pgv_lang["message_email3"]		= "Você enviou esta mensagem para o Administrador:";
$pgv_lang["viewing_url"]			= "Esta mensagem foi enviada quando navegava pelo seguinte endereço: ";
$pgv_lang["messaging2_help"]			= "Quando você confirmar o envio desta mensagem você também receberá uma cópia no endereco de seu e-mail.";
$pgv_lang["random_picture"]		= "Imagem Aleatória";
$pgv_lang["message_instructions"]		= "<b>Importante:</b> Informações privadas de pessoas vivas somente serão fornecidas aos parentes e amigos próximos.  Será solicitado confirmação de seu parentesco antes de você receber qualquer dado privado.  Ocasionalmente informações de pessoas já falecidas podem ser privadas.  Se esse for o caso, é porquê não há informação suficiente a respeito da pessoa que permita determinar se a mesma está viva ou não e nós provavelmente não temos mais informações sobre essa pessoa.<br /><br />Antecipadamente, por favor, verifique se o que está pesquisando sobre a pessoa está correto, verificando datas, locais e parentesco.  Se você está enviando alterações sobre dados de genealogia, por favor informe as fontes de onde obteve os dados.<br /><br />";
$pgv_lang["sending_to"]				= "Esta mensagem será enviada para #TO_USER#";
$pgv_lang["preferred_lang"]	 		= "Este usuário prefere receber mensagens em #USERLANG#";
$pgv_lang["gedcom_created_using"]		= "Este arquivo de Árvore Genealógica foi criado usando <b>#SOFTWARE# #VERSION#</b>.";
$pgv_lang["gedcom_created_on"]		= "Este arquivo de Árvore Genealógica foi criado em <b>#DATE#</b>.";
$pgv_lang["gedcom_created_on2"]		= " em <b>#DATE#</b>";
$pgv_lang["gedcom_stats"]			= "Dados Estatísticos";
$pgv_lang["stat_individuals"]			= "Pessoas, ";
$pgv_lang["stat_families"]			= "Famílias, ";
$pgv_lang["stat_sources"]			= "Fontes, ";
$pgv_lang["stat_other"]				= "Outros registros";
$pgv_lang["customize_page"]			= "Configurar Meu Portal";
$pgv_lang["customize_gedcom_page"]		= "Configurar esta Página Inicial";
$pgv_lang["upcoming_events_block"]		= "Próximos Eventos";
$pgv_lang["upcoming_events_descr"]		= "o bloco 'Próximos Eventos' mostra uma lista dos eventos deste banco de dados que ocorrerão dentro dos próximos 30 dias.  Em <b>Meu Portal</b> o bloco somente exibirá a lista das pessoas vivas.  Na <b>Página Inicial</b> exibirá a lista de todas as pessoas.";
$pgv_lang["todays_events_block"]		= "Este dia em nossa História";
$pgv_lang["todays_events_descr"]		= "o bloco 'Este dia em nossa História' mostra uma lista de eventos deste banco de dados que ocorram hoje.  Se não for encontrado nenhum evento, este bloco não será exibido.  Em <b>Meu Portal</b> o bloco somente exibirá a lista das pessoas vivas.  Na <b>Página Inicial</b> exibirá a lista de todas as pessoas.";
$pgv_lang["logged_in_users_block"]		= "Usuários Conectados";
$pgv_lang["logged_in_users_descr"]		= "O bloco 'Usuários Conectados' exibe a lista dos usuários que estão conectados.";
$pgv_lang["user_messages_block"]		= "Minhas Mensagens";
$pgv_lang["user_messages_descr"]		= "O bloco 'Minhas Mensagens' exibe a lista das mensagens que foram enviadas ao usuário.";
$pgv_lang["user_favorites_block"]		= "Meus Favoritos";
$pgv_lang["user_favorites_descr"]		= "O bloco 'Meus Favoritos' exibe a lista de favoritos do usuário no sistema. Isto permite um fácil acesso aos mesmos.";
$pgv_lang["welcome_block"]			= "Bem-Vindo - Usuário";
$pgv_lang["welcome_descr"]			= "O bloco 'Bem-Vindo - Usuário' exibe o nome do usuário, data e hora corrente, bem como links que permitem acessar rapidamente a sua conta, seus dados, sua árvore genealógica e link para customizar a página <b>Meu Portal<b/>.";
$pgv_lang["random_media_block"]		= "Imagem Aleatória";
$pgv_lang["random_media_descr"]		= "O bloco 'Imagem Aleatória' seleciona aleatoriamente uma foto ou outro objeto multimídia do banco de dados e exibe ao usuário.";
$pgv_lang["gedcom_block"]			= "Bem-Vindo - Site";
$pgv_lang["gedcom_descr"]			= "O bloco 'Bem-Vindo - Site' funciona da mesma forma que o o bloco 'Bem-Vindo - Usuário'. Exibe o titulo do arquivo de genealogia, bem como a data e hora corrente.";
$pgv_lang["gedcom_favorites_block"]		= "Favoritos - Site";
$pgv_lang["gedcom_favorites_descr"]		= "O bloco 'Favoritos - Site' permite ao administrador a  capacidade de selecionar suas pessoas favoritas do banco de dados de forma que os visitantes possam facilmente encontrá-los. Esta é uma forma de evidenciar as pessoas que são importantes em sua história.";
$pgv_lang["gedcom_stats_block"]		= "Dados Estatísticos";
$pgv_lang["gedcom_stats_descr"]		= "O bloco 'Dados Estatísticos' exibe ao visitante algumas informações básicas sobre do banco de dados como por exemplo quando foi criado e quantas pessoas estão cadastradas no mesmo e sobrenomes mais comuns.";
$pgv_lang["portal_config_intructions"]	= "Aqui você pode customizar a página <b>Meu Portal</b>  posicionando os blocos na página da maneira que quiser.  A página é dividida em duas seções: A seção  'Principal' e a seção 'Auxiliar'.  Os blocos da seção 'Principal' são mostrados em maior tamanho e abaixo do título da página.  A seção 'Auxiliar' ocupa o lado direito da página.  Cada seção tem a sua própria lista de blocos que serão mostrados na página na ordem que foram relacionados.  Você pode adicionar, remover e reordenar de acordo com sua preferência.";
$pgv_lang["login_block"]			= "Conectar";
$pgv_lang["login_descr"]			= "O bloco 'Conectar' solicita Nome e Senha para os usuários conectaram-se ao site.";
$pgv_lang["theme_select_block"] 		= "Seleção de Temas";
$pgv_lang["theme_select_descr"] 		= "O bloco Seleção de Temas permite selecionar outro tema para o site.";   // rever aqui
$pgv_lang["block_top10_title"]		= "Sobrenomes mais Populares";
$pgv_lang["block_top10"]			= "Sobrenomes Populares";
$pgv_lang["block_top10_descr"]		= "Este bloco mostra uma tabela com os 10 sobrenomes mais populares do banco de dados.";

$pgv_lang["gedcom_news_block"]		= "Notícias";
$pgv_lang["gedcom_news_descr"]		= "O bloco 'Notícias' mostrará aos visitantes notícias ou artigos publicados pelo usuário Administrador.  'Notícias' é um bom local para anunciar alterações no banco de dados ou uma reunião familiar.";
$pgv_lang["user_news_block"]			= "Meu Jornal";
$pgv_lang["user_news_descr"]			= "O bloco 'Meu Jornal' permite ao usuário a manutenção de notícias ou um Jornal on-line.";
$pgv_lang["my_journal"]				= "Meu Jornal";
$pgv_lang["no_journal"]				= "Você ainda não criou nenhuma entrada no Jornal.";
$pgv_lang["confirm_journal_delete"]		= "Você tem certeza que quer excluir essa entrada?";
$pgv_lang["add_journal"]			= "Adicionar nova entrada ao Jornal";
$pgv_lang["gedcom_news"]			= "Notícias";
$pgv_lang["confirm_news_delete"]		= "Você tem certeza que quer excluir essa notícia?";
$pgv_lang["add_news"]				= "Adicionar Notícia";
$pgv_lang["no_news"]				= "No News Articles have been submitted.";
$pgv_lang["edit_news"]				= "Adicionar/Editar Jornal/Notícias";
$pgv_lang["enter_title"]			= "Favor informar um título.";
$pgv_lang["enter_text"]				= "Favor informar algum texto para essa Notícia ou Jornal.";
$pgv_lang["news_saved"]				= "Notícia/Jornal salvo com sucesso.";
$pgv_lang["article_text"]			= "Texto";
$pgv_lang["main_section"]			= "Blocos da seção Principal";
$pgv_lang["right_section"]			= "Blocos da seção Auxiliar";
$pgv_lang["move_up"]				= "Mover Cima";
$pgv_lang["move_down"]				= "Mover Baixo";
$pgv_lang["move_right"]				= "Mover Direita";
$pgv_lang["move_left"]				= "Mover Esquerda";
$pgv_lang["add_main_block"]			= "Adicionar bloco na seção Principal";
$pgv_lang["add_right_block"]			= "Adicionar bloco na seção Auxiliar";
$pgv_lang["broadcast_all"]			= "Enviar para todos os usuários";
$pgv_lang["hit_count"]				= "Nº de Visitas:";
$pgv_lang["phpgedview_message"]		= "Mensagem PhpGedView";
$pgv_lang["common_surnames"]			= "Sobrenomes Mais Comuns";
$pgv_lang["default_news_title"]		= "Bem-vindo à sua Árvore Genealógica ";
$pgv_lang["default_news_text"]		= "As informações de genealogia deste website são produzidas  por <a href=\"http://www.phpgedview.net/\" target=\"_blank\">PhpGedView #VERSION#</a>.  Esta página fornece uma introdução e visão geral desta genealogia.<br /><br />Para explorar as informações do site você pode escolher um dos gráficos a partir do menu de <b>Gráficos</b>, mostrar <b>Listas</b> de pessoas, famílias, locais, objetos multimídia ou também <b>Pesquisar</b> um nome ou local.<br /><br />Se tiver dúvida ao usar o site, clicando no menu de <b>Ajuda</b> você receberá informações de como usar a página que está sendo visualizada no momento.<br /><br />Obrigado por visitar este site !!!";
$pgv_lang["reset_default_blocks"]		= "Restaurar padrão de Blocos";
$pgv_lang["recent_changes"]			= "Últimas Atualizações";
$pgv_lang["recent_changes_block"]		= "Últimas Atualizações";
$pgv_lang["recent_changes_descr"]		= "O bloco 'Últimas Alterações' mostrará uma lista das últimas atualizações que foram efetuadas no banco de dados no último mês.  Este bloco pode ajudá-lo a ficar em dia com as mudanças do site.  As mudanças se baseiam na expressão CHAN.";
$pgv_lang["delete_selected_messages"]	= "Excluir Mensagens Selecionadas";
$pgv_lang["use_blocks_for_default"]		= "Usar esses blocos como padrão de configuração para todos os usuários?";

//-- upgrade.php messages
$pgv_lang["upgrade_util"]			= "Utilitário de Alterações";
$pgv_lang["no_upgrade"]				= "Não há arquivos a atualizar.";
$pgv_lang["use_version"]			= "Você está usuando a versão:";
$pgv_lang["current_version"]		=	"Versão estável corrente:";
$pgv_lang["upgrade_download"]			= "Download:";
$pgv_lang["upgrade_tar"]			= "TAR";
$pgv_lang["upgrade_zip"]			= "ZIP";
$pgv_lang["latest"]				= "Você está rodando a última versão do PhpGedView.";
$pgv_lang["location"]				= "Localização dos arquivos de atualização: ";
$pgv_lang["include"]				= "Incluir:";
$pgv_lang["options"]			= "Opções:";
$pgv_lang["inc_phpgedview"]			= " PhpGedView";
$pgv_lang["inc_languages"]			= " Idiomas";
$pgv_lang["inc_config"]				= " Arquivo de configuração";
$pgv_lang["inc_index"]				= " Arquivo de Índice";
$pgv_lang["inc_themes"]				= " Temas";
$pgv_lang["inc_docs"]				= " Manuais";
$pgv_lang["inc_privacy"]			= " Arquivo(s) privado(s)";
$pgv_lang["inc_backup"]				= " Criar Backup";
$pgv_lang["upgrade_help"]			= " Ajude-me";
$pgv_lang["cannot_read"]			= "Não foi possível ler arquivo:";
$pgv_lang["not_configured"]			= "Você ainda não configurou o PhpGedView.";
$pgv_lang["location_upgrade"]			= "Por favor, preencha a localizaçào de seus arquivos de atualização.";
$pgv_lang["new_variable"]			= "Encontrado nova variável: ";
$pgv_lang["config_open_error"]		= "Ocorreu um erro na abertura do arquivo de configuração.";
$pgv_lang["config_write_error"] 		= "Erro!!! Não foi possível gravar arquivo de configuração.";
$pgv_lang["config_update_ok"]			= "Arquivo de configuração atualizado com sucesso.";
$pgv_lang["config_uptodate"]		= "Sua configuração está atualizada.";
$pgv_lang["processing"]				= "Processando...";
$pgv_lang["privacy_open_error"] 		= "Ocorreu um erro na abertura no arquivo  [#PRIVACY_MODULE#].";
//////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["privacy_write_error"]		= "Erro!!! Impossível gravar o arquivo [#PRIVACY_MODULE#].<br />Certifique-se de disponibilizar permissão de escrita para o arquivo.<br />Permissions may be restored once privacy file is written.";
//////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["privacy_update_ok"]		= "Arquivo de privacidade: [#PRIVACY_MODULE#] alterado com sucesso.";
$pgv_lang["privacy_uptodate"]			= "Seu arquivo [#PRIVACY_MODULE#] está atualizado.";
$pgv_lang["heading_privacy"]			= "Arquivo(s) de privacidade:";
$pgv_lang["heading_phpgedview"]		= "Arquivos PhpGedView:";
$pgv_lang["heading_image"]			=	"Arquivos de Imagens:";
$pgv_lang["heading_index"] 			= "Arquivos de Índices:";
$pgv_lang["heading_language"]		= "Arquivos de Idiomas:";
$pgv_lang["heading_theme"]			= "Arquivos de Temas:";
$pgv_lang["heading_docs"]			= "Manuais:";
$pgv_lang["copied_success"]			= "copiado com sucesso";
$pgv_lang["backup_copied_success"]		= "arquivo de backup criado com sucesso.";
$pgv_lang["folder_created"]			= "Pasta criada";
//////////////////////////////////////////////////////////////////////////////////////////
$pgv_lang["process_error"]			= "There is a problem processing the page. A newer version cannot be determined.";
$pgv_lang["upgrade_completed"]		= "Upgrade Completed Successfully";
$pgv_lang["start_using_upgrad"]		= "Click here to begin using version";
//////////////////////////////////////////////////////////////////////////////////////////

//-- validate GEDCOM
$pgv_lang["performing_validation"]		= "Executando validação GEDCOM, selecione as opções desejadas e clique em 'Limpeza'";
$pgv_lang["changed_mac"]			= "Detectado 'Macintosh line endings'. Alterado 'lines ending' com somente 'return' e no final com um 'return' e um 'linefeed'.";
$pgv_lang["changed_places"]			= "Detectado codificação inválida de Local. Colocado em ordem registros para adequar às especificações GEDCOM 5.5.  Um exemplo de seu GEDCOM é:";
$pgv_lang["invalid_dates"]			= "Detectado formato inválido de datas, na 'Limpeza' esses dados serão modificados para o formato DD MMM YYYY (ex. 1 JAN 2004).";
$pgv_lang["valid_gedcom"]			= "Detectado GEDCOM válido.  Limpeza não requerida.";
$pgv_lang["optional_tools"]			= "Você pode também executar 'Ferramentas Opcionais' antes da importação.";
$pgv_lang["optional"]				= "Ferramentas Opcionais";
$pgv_lang["date_format"]			= "Formato da Data";
$pgv_lang["day_before_month"]			= "Dia antes do Mês (DD MM YYYY)";
$pgv_lang["month_before_day"]			= "Mês antes do Dia (MM DD YYYY)";
$pgv_lang["do_not_change"]			= "Não alterar";
$pgv_lang["change_id"]				= "Alterar ID Pessoa para";
$pgv_lang["example_date"]			= "Exemplo de data inválida do seu GEDCOM:";
$pgv_lang["add_media_tool"]			= "Ferramenta Adicionar Mídia";
$pgv_lang["launch_media_tool"]		= "Clique aqui para executar Ferramenta Adicionar Mídia.";
$pgv_lang["add_media_descr"]			= "Esta ferramenta irá adicionar 'OBJE tags' ao GEDCOM.  Fechar essa janela quando você finalizar a adição da mídia.";
$pgv_lang["highlighted"]			= "Imagem realçada";
$pgv_lang["extension"]				= "Extensão";
$pgv_lang["order"]				= "Ordem";
$pgv_lang["add_media_button"]			= "Adicionar Mídia";
$pgv_lang["media_table_created"]		= "Atualizada tabela <i>media</i> com sucesso.";
$pgv_lang["click_to_add_media"]		= "Clique aqui para Adicionar Mídia informada acima ao GEDCOM #GEDCOM#";
$pgv_lang["adds_completed"]			= "Mídia adiconada com sucesso ao arquivo GEDCOM.";
$pgv_lang["ansi_encoding_detected"]		= "Detectado arquivo com configuração ANSI.  PhpGedView trabalha melhor com arquivos configurados em UTF-8.";
$pgv_lang["invalid_header"]			= "Detectado linhas antes do cabeçalho do GEDCOM (0 HEAD).  Na 'Limpeza' essas linhas serão removidas.";
$pgv_lang["macfile_detected"]			= "Detectado arquivo Macintosh.  Na 'Limpeza' seu arquivo será convertido para um arquivo DOS.";
$pgv_lang["place_cleanup_detected"]		= "Detectado codificação inválida de Local.  Esses erros precisariam ser corrigidos.  O exemplo seguinte mostra o local inválido que foi detectado: ";
$pgv_lang["cleanup_places"]			= "Limpeza de Locais";
$pgv_lang["empty_lines_detected"]		= "Detectado linhas vazias em seu arquivo GEDCOM.  Na 'Limpeza' essas linhas vazias serão removidas.";

//-- hourglass chart
$pgv_lang["hourglass_chart"]			= "Gráfico Hourglass";

//-- report engine
$pgv_lang["choose_report"]			= "Escolha o Relatório a executar";
$pgv_lang["enter_report_values"]		= "Informe os dados do Relatório";
$pgv_lang["selected_report"]			= "Relatório selecionado";
$pgv_lang["run_report"] 			= "Ver Relatório";
$pgv_lang["select_report"]			= "Selecione Relatório";
$pgv_lang["download_report"]			= "Salvar Relatório";
$pgv_lang["reports"]				= "Relatórios";
$pgv_lang["pdf_reports"]			= "Relatórios PDF";
$pgv_lang["html_reports"]			= "Relatórios HTML";
$pgv_lang["family_group_report"]		= "Relatório de Grupo Familiar";
$pgv_lang["page"]					= "Página";
$pgv_lang["of"] 					= "de";
$pgv_lang["enter_famid"]			= "Informe ID da Família";
$pgv_lang["show_sources"]			= "Mostrar fontes?";
$pgv_lang["show_notes"] 			= "Mostrar notas?";
$pgv_lang["show_basic"] 			= "Imprimir eventos quando em branco?";
$pgv_lang["show_photos"]			= "Mostrar fotos?";
$pgv_lang["individual_report"]		= "Relatório de Pessoa";
$pgv_lang["enter_pid"]				= "Informe ID Pessoa";
$pgv_lang["individual_list_report"]		= "Individual List Report";
$pgv_lang["generated_by"]			= "Gerado por";
$pgv_lang["list_children"]			= "Lista em ordem de nascimento.";
$pgv_lang["birth_report"]			= "Relatório Data e Local de Nascimento";
$pgv_lang["birthplace"]				= "Local de Nascimento contém";
$pgv_lang["birthdate1"]				= "Data de Nascimento inicial";
$pgv_lang["birthdate2"]				= "Data de Nascimento final";
$pgv_lang["sort_by"]				= "Classificado por";

$pgv_lang["cleanup"]				= "Limpeza";
$pgv_lang["skip_cleanup"]			= "Não executar Limpeza";

//-- CONFIGURE (extra) messages for programs patriarch, slklist and statistics
$pgv_lang["dynasty_list"]			= "Visão geral de famílias";
$pgv_lang["make_slklist"]			= "Criar lista EXCEL (SLK)";
$pgv_lang["excel_list"]				= "Saída em formato EXCEL (SLK) dos subsequentes arquivos (primeiro uso patriarchlist):";
$pgv_lang["excel_tab"]				= "tabblad:";
$pgv_lang["excel_create"]			= " será criado no seguinte arquivo:";
$pgv_lang["patriarch_list"]			= "Lista Patriarch";
$pgv_lang["slk_list"]				= "Lista EXCEL SLK";
$pgv_lang["statistics"]				= "Estatísticas";

//-- Merge Records
$pgv_lang["merge_records"]			= "Consolidar Registros";
$pgv_lang["merge_same"] 			= "Registros não são do mesmo tipo.  Não é possível consolidar registros que não sejam do mesmo tipo.";
$pgv_lang["merge_step1"]			= "Consolidação Passo 1 de 3";
$pgv_lang["merge_step2"]			= "Consolidação Passo 2 de 3";
$pgv_lang["merge_step3"]			= "Consolidação Passo 3 de 3";
$pgv_lang["select_gedcom_records"]		= "Selecionar 2 registros GEDCOM para consolidação.  Registros devem ser do mesmo tipo.";
$pgv_lang["merge_to"]				= "Consolidar para ID:";
$pgv_lang["merge_from"] 			= "Consolidar de ID:";
$pgv_lang["merge_facts_same"]			= "Os fatos seguintes são exatamente os mesmos em ambos registros e serão automaticamente consolidados";
$pgv_lang["no_matches_found"]			= "Não encontrada nenhuma combinação de fatos";
$pgv_lang["unmatching_facts"]			= "Os fatos seguintes não combinam.	Selecione a informação que você deseja manter.";
$pgv_lang["record"] 				= "Registro";
$pgv_lang["adding"] 				= "Adicionando";
$pgv_lang["updating_linked"]			= "Atualizando registro combinado";
$pgv_lang["merge_more"] 			= "Consolidar mais registros.";
$pgv_lang["same_ids"]				= "Você informou os mesmos IDs.  Você não pode consolidar os mesmos registros.";

//-- ANCESTRY FILE MESSAGES
$pgv_lang["ancestry_chart"] 			= "Ancestrais";
$pgv_lang["gen_ancestry_chart"]		= "#PEDIGREE_GENERATIONS# Gerações de Ancestrais";
$pgv_lang["chart_style"]			= "Tipo de gráfico";
$pgv_lang["ancestry_list"]			= "Estilo - Lista";
$pgv_lang["ancestry_booklet"]   		= "Estilo - Livreto";
// 1ª generação
$pgv_lang["sosa_2"] 				= "Pai";
$pgv_lang["sosa_3"] 				= "Mãe";
// 2ª generação
$pgv_lang["sosa_4"] 				= "Avô";
$pgv_lang["sosa_5"] 				= "Avó";
$pgv_lang["sosa_6"] 				= "Avô";
$pgv_lang["sosa_7"] 				= "Avó";
// 3ª generação
$pgv_lang["sosa_8"] 				= "Bisavô";
$pgv_lang["sosa_9"] 				= "Bisavó";
$pgv_lang["sosa_10"]				= "Bisavô";
$pgv_lang["sosa_11"]				= "Bisavó";
$pgv_lang["sosa_12"]				= "Bisavô";
$pgv_lang["sosa_13"]				= "Bisavó";
$pgv_lang["sosa_14"]				= "Bisavô";
$pgv_lang["sosa_15"]				= "Bisavó";
// 4ª generação
$pgv_lang["sosa_16"]				= "Trisavô";
$pgv_lang["sosa_17"]				= "Trisavó";
$pgv_lang["sosa_18"]				= "Trisavô";
$pgv_lang["sosa_19"]				= "Trisavó";
$pgv_lang["sosa_20"]				= "Trisavô";
$pgv_lang["sosa_21"]				= "Trisavó";
$pgv_lang["sosa_22"]				= "Trisavô";
$pgv_lang["sosa_23"]				= "Trisavó";
$pgv_lang["sosa_24"]				= "Trisavô";
$pgv_lang["sosa_25"]				= "Trisavó";
$pgv_lang["sosa_26"]				= "Trisavô";
$pgv_lang["sosa_27"]				= "Trisavó";
$pgv_lang["sosa_28"]				= "Trisavô";
$pgv_lang["sosa_29"]				= "Trisavó";
$pgv_lang["sosa_30"]				= "Trisavô";
$pgv_lang["sosa_31"]				= "Trisavó";
// 5ª generação
$pgv_lang["sosa_32"]				= "Tataravó";
$pgv_lang["sosa_33"]				= "Tataravô";
$pgv_lang["sosa_34"]				= "Tataravó";
$pgv_lang["sosa_35"]				= "Tataravô";
$pgv_lang["sosa_36"]				= "Tataravó";
$pgv_lang["sosa_37"]				= "Tataravô";
$pgv_lang["sosa_38"]				= "Tataravó";
$pgv_lang["sosa_39"]				= "Tataravô";
$pgv_lang["sosa_40"]				= "Tataravó";
$pgv_lang["sosa_41"]				= "Tataravô";
$pgv_lang["sosa_42"]				= "Tataravó";
$pgv_lang["sosa_43"]				= "Tataravô";
$pgv_lang["sosa_44"]				= "Tataravó";
$pgv_lang["sosa_45"]				= "Tataravô";
$pgv_lang["sosa_46"]				= "Tataravó";
$pgv_lang["sosa_47"]				= "Tataravô";
$pgv_lang["sosa_48"]				= "Tataravó";
$pgv_lang["sosa_49"]				= "Tataravô";
$pgv_lang["sosa_50"]				= "Tataravó";
$pgv_lang["sosa_51"]				= "Tataravô";
$pgv_lang["sosa_52"]				= "Tataravó";
$pgv_lang["sosa_53"]				= "Tataravô";
$pgv_lang["sosa_54"]				= "Tataravó";
$pgv_lang["sosa_55"]				= "Tataravô";
$pgv_lang["sosa_56"]				= "Tataravó";
$pgv_lang["sosa_57"]				= "Tataravô";
$pgv_lang["sosa_58"]				= "Tataravó";
$pgv_lang["sosa_59"]				= "Tataravô";
$pgv_lang["sosa_60"]				= "Tataravó";
$pgv_lang["sosa_61"]				= "Tataravô";
$pgv_lang["sosa_62"]				= "Tataravó";
$pgv_lang["sosa_63"]				= "Tataravô";

//-- FAN CHART
$pgv_lang["fan_chart"]				= "Gráfico de Ascendência";
$pgv_lang["gen_fan_chart"]			= "#PEDIGREE_GENERATIONS# Gerações no Gráfico de Ascendência";
$pgv_lang["fan_width"]				= "Largura";
$pgv_lang["gd_library"]				= "Servidor PHP desconfigurado: Biblioteca GD requirida para uso de funções de imagem.";
$pgv_lang["gd_freetype"]			= "Servidor PHP desconfigurado: Biblioteca Freetype requirida para fontes TrueType.";
$pgv_lang["gd_helplink"]			= "http://www.php.net/gd";
$pgv_lang["fontfile_error"]			= "Arquivo de Fonte não encontrada no servidor PHP";

//-- RSS Feed

$pgv_lang["rss_descr"]				= "Notícias e links do site #GEDCOM_TITLE#";
$pgv_lang["rss_logo_descr"]			= "Feed criado por PhpGedView";

if (file_exists($PGV_BASE_DIRECTORY . "languages/lang.en.extra.php")) require $PGV_BASE_DIRECTORY . "languages/lang.en.extra.php";
?>
