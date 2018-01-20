ivProgApp.filter('i18n', ['$rootScope', function($rootScope) {
  return function (input) {
    var translations = {
      'pt' : {
        'Welcome' : 'Bem-vindo',
        'LoadExercise' : 'Carregar um exercício existente...',
        'ChooseOneOptionToContinue' : 'escolha uma opção a seguir para continuar:',
        'CreateNewExercise' : 'Criar um novo exercício...',
        'BackButton' : 'voltar',
        'ENGLISH' : 'English',
        'msg_testcases' : 'Casos de teste (para autoria de exercícios)',
        'button_eval' : 'Avaliar',
        'alt_button_eval' : 'clique aqui para avaliar seu exercicio',
        'button_create_var' : 'Variáveis',
        'alt_button_create_var' : 'clique aqui para criar nova variável',
        'button_create_cmd' : 'Comando',
        'alt_button_create_cmd' : 'clique aqui para inserir novo comando/instrução',
        'button_create_clearoutputs' : 'Limpar console',
        'alt_button_create_clearoutputs' : 'clique aqui para limpar a área de saída de dados/mensagenso',
        'button_create_testcases' : '+ Adicionar caso de teste',
        'alt_button_create_testcases' : 'clique aqui para adicionar novo caso de teste (conjunto de entradas/saídass)',
        'cmd_cmd' : 'Comando',
        'cmd_if' : 'Se verdadeiro então',
        'cmd_repeatN' : 'Repita N vezes',
        'cmd_while' : 'Enquanto verdadeiro',
        'cmd_input' : 'Leitura de dados',
        'cmd_output' : 'Escrita de dados'
        },
      'en' : {
        'Welcome' : 'Welcome',
        'LoadExercise' : 'Load an exercise...',
        'ChooseOneOptionToContinue' : 'Choose one option to continue:',
        'CreateNewExercise' : 'Create a new exercise...',
        'BackButton' : 'back',
        'ENGLISH' : 'english',
        'msg_testcases' : 'Test cases (for assessment authoring)',
        'button_eval' : 'Evaluate',
        'alt_button_eval' : 'click here to evaluate your exercise',
        'button_create_var' : 'Variables',
        'alt_button_create_var' : 'click here to create a new variable',
        'button_create_cmd' : 'Command',
        'alt_button_create_cmd' : 'click here to insert new command/instruction',
        'button_create_clearoutputs' : 'Console clear',
        'alt_button_create_clearoutputs' : 'click here to clear all data/messages from console',
        'button_create_testcases' : '+ Add new test case',
        'alt_button_create_testcases' : 'click here to add a new test case (set of inputs and outputs)',
        'cmd_cmd' : 'Comand',
        'cmd_if' : 'If true then',
        'cmd_repeatN' : 'Repeat N times',
        'cmd_while' : 'While true',
        'cmd_input' : 'Data input',
        'cmd_output' : 'Data output'
      }
    },
    currentLanguage = $rootScope.currentLanguage || 'pt';
    return translations[currentLanguage][input];
  }
}]);
