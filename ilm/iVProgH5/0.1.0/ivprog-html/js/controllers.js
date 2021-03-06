var deferred = Deferred();

// To avoid several load, 'isContentEmpty' and 'countEmpty' are declared in 'js/app.js'
//    var isContentEmpty = 0;
//    
var countEmpty = 0;


function StartCtrl(){
  //D alert('1: StartCtrl! ' + countEmpty);
}

function CommCtrl($scope, $rootScope){
  $scope.valor = -12345; // mark of emptiness
  $scope.getSource = function(){
    return JSON.stringify($rootScope.getSource());
  }
  $scope.getEvaluation = function(){
    return $rootScope.getEvaluation();
  }

  //D alert('2: CommCtrl! ' + countEmpty);
}


// @calledby: ./js/app.js : ivProgApp.config(...)?
// This provides the actions 'getEvaluation', 'run=true'
// This function is called several times (to each element)
function IvProgCreateCtrl($scope, $rootScope, IvProgSource, $filter) {

  //alert('3: IvProgCreateCtrl! ' + $scope + ', ' + $rootScope + ', ' + $filter);

  $rootScope.trackAction = function(action){
    //D alert('<ivprogh5>js/controllers.js: action=' + action);
    if(iLMparameters.iLM_PARAM_ServerToGetAnswerURL!=null && iLMparameters.iLM_PARAM_ServerToGetAnswerURL!=''){
      $.post(iLMparameters.iLM_PARAM_ServerToGetAnswerURL+"&track=1", { trackingData: "html=1;"+action }, function(d){});
      }
  }

  // iLM : getAnswer()
  $rootScope.getSource = function(){
    //D alert('<ivprogh5>js/controllers.js: rootScope.getSource');
    $rootScope.trackAction("getSource");
    return { 
      mapping: $rootScope.mapping, 
      src: $scope.program,
      testCases: $scope.testCases
    };
  }

  // iLM : getEvaluation()
  $rootScope.getEvaluation = function(){
    //D alert('<ivprogh5>js/controllers.js: rootScope.getEvaluation()');
    $rootScope.trackAction("getEvaluation");
    $scope.run(true);
  }

  $rootScope.itemCount = 0;
  $scope.vars = [];
  $scope.params = [];
  $scope.testCases = [];
  $scope.addTestCase = function(){
    $rootScope.trackAction("addTestCase");
    $scope.testCases.push({ input: "", output: "", currentIndex: 0 });
  }

  $scope.removeTestCase = function(i){
    $rootScope.trackAction("removeTestCase");
    $scope.testCases.splice(i, 1);
  }

  $rootScope.mapping = {};

  $scope.getTeste = function(){
    return 1;
  }

  // undo - redo control
  $scope.historyStack = -1;
  $scope.actionsHistory = [];
  $scope.addSnap = true;

  $scope.takeSnap = function(friendlyName, applying, sp){
    if(sp){
      $scope.actionsHistory.splice($scope.historyStack, $scope.actionsHistory.length-$scope.historyStack);
    }
    $scope.actionsHistory.push({name: friendlyName, src: JSON.stringify($scope.program)});
    $scope.historyStack = $scope.actionsHistory.length;
  }

  $rootScope.snapshot = function(friendlyName, applying){
    if(!applying){
      $scope.$apply(function(){
        $scope.takeSnap(friendlyName, applying, true);
      });
    }else{
      $scope.takeSnap(friendlyName, applying, true);
    }
    $scope.addSnap = true;
  }

  $scope.undo = function(){
    if($scope.historyStack>0){
      if($scope.addSnap){
        // salvando o estado atual
        $scope.takeSnap('', 1);
        $scope.historyStack--;
        $scope.addSnap = false;
      }
      $scope.historyStack--;
      var obj = JSON.parse($scope.actionsHistory[$scope.historyStack].src);
      $scope.program = obj;
    }
  }

  $scope.redo = function(){
    if($scope.historyStack < $scope.actionsHistory.length-1){
      $scope.historyStack++;
      var obj = JSON.parse($scope.actionsHistory[$scope.historyStack].src);

      $scope.program = obj;
    }
  }

  $scope.currentFunction = 0;

  $scope.program = {
    programName: "firstProgram",
    functions: [
      {
        isMain: true,
        name: "Principal",
        vars: {},
        params: {},
        type: "main", // int, void, float
        nodes:[]
      }/*,
      {
        isMain: false,
        name: "fatorial",
        vars: {},
        varss: {
          "var_1":
            { name: 'newVar1', type: 'int', initialValue: 0, id: "var_1" }
        },
        params: {},
        type: "int", // int, void, float
        nodes:[],
        nodess: [
          {
              id: "attr_1",
              type: "attr",
              name: "attr",
              parent: null,
              variable: "",
              exp: []
            }
        ]
      }*/
    ]
  }; // $scope.program

  $scope.setCurrentFunction = function(ind){
    $scope.currentFunction = ind;
  }

  $scope.addElVar = function(v){
    v.push({
            t: "var",
            v: "",
            o: "",
            p: ''//v
          });
  }

  $scope.addElVal = function(v){
    v.push({
            t: "val",
            v: 0,
            o: "",
            p: ''//v
          });
  }

  $scope.addElExpB = function(v){
    v.push({
            t: "expB",
            v: {
    op1: {
      t: "v",
      v: ""
    },
    op2: {
      t: "v",
      v: ""
    },
    op: ">"
            },
            o: "&&",
            p: ''//v
          });
  }

  $scope.isolar = function(item){
    item.t = "exp";
    item.v = "";
    item.exp = [];
  }

  $scope.addExp = function(parent){
    parent.push({ t: "val", v: "a", o: "+"});
  }

  $scope.getTemplate = function(x){
    return 'partials/elements/'+x.type+'.html'+"?t="+cacheTime;
  }

  $scope.addParam = function(){
    //var ind = $scope.params.length;
    var ind = $scope.programs[$scope.currentProgram].functions[0].params.length;

    //$scope.params.push({ name: 'newParam'+ind, type: 'int', initialValue: 0 } );
    $scope.programs[$scope.currentProgram].functions[0].params.push({ name: 'newParam'+ind, type: 'int', initialValue: 0 } );
  }

  $scope.removeParam = function(v){
    $scope.params.splice($scope.params.indexOf(v), 1);
  }

  $scope.varSetType = function(v, type){
    $rootScope.trackAction("changeVarType");

    var previousType = v.type;
    v.type = type;

    if(type=="string"){
      v.initialValue = "Olá mundo!";
    }else if(type=="float"){
      v.initialValue = 1.0;
    }else if(type=="int"){
      v.initialValue = 1;
    }else if(type=="boolean"){
      v.initialValue = true;
    }
    $scope.checkChangeTypeConsequences(v, $scope.program.functions[$scope.currentFunction].nodes, previousType);
  }

  // When the variable type is change this could be have consequences, verify them!
  $scope.checkChangeTypeConsequences = function(variable, where, previous){
    angular.forEach(where, function(item, key){
      if(item.type=="attr"){
        if(item.variable==variable.id){
          if(variable.type!=previous){
            var compatibility = ["int", "float"];
            if((compatibility.indexOf(variable.type)==-1)||(compatibility.indexOf(previous)==-1)){
              if(where[key].exp.length>0){
                where[key].exp = [];
              }  
            }
          }
        }
      }
      if(item.nodes && item.nodes.length>0){
        $scope.checkChangeTypeConsequences(variable, item.nodes);
      }
    });
  } // $scope.checkChangeTypeConsequences = function(variable, where, previous)

  $scope.addVar = function(){
    $rootScope.trackAction("addVar");
    // TODO: checar se alterou o valor
        $rootScope.snapshot('Variável adicionada', true);
    var ind = $scope.itemCount;
    var id = "var"+$scope.itemCount++;
    $scope.program.functions[$scope.currentFunction].vars[id] = ({ name: 'newVar'+ind, type: 'int', initialValue: 1, id: id });
  }

  $scope.removeVarRec = function(nodes, id){
    $rootScope.trackAction("removeVar");
    angular.forEach(nodes, function(node, key){

      if(node.type=="write"){
        if(node.variable==id){
          node.variable = '';
        }
      }
      if(node.type!="attr"){
        if(node.nodes.length>0){
          $scope.removeVarRec(node.nodes, id);
        }
      }
    });
  }

  $scope.removeVar = function(v){
    $rootScope.trackAction("removeVar");
    $rootScope.snapshot('Variável removida', true);
    $scope.removeVarRec($scope.program.functions[$scope.currentFunction].nodes, v.id);
    delete $scope.program.functions[$scope.currentFunction].vars[v.id];
  }

  $scope.removeItem = function(parent, item){
    $rootScope.trackAction("removeItem");

    parentId = parent;
    // TODO: tratar para os outros functions
    if(parent=="root_0"){
      parent = $scope.program.functions[0].nodes;
    }else{
      parent = $rootScope.mapping[parent].nodes;
    }
    if($.isArray(parent)) {
      parent.splice(parent.indexOf(item),1);
    }
    if($rootScope.mapping[parentId]){
      var p1 = $rootScope.mapping[parentId].nodes1;
      if($.isArray(p1)) {
        p1.splice(p1.indexOf(item),1);
      }
      var p2 = $rootScope.mapping[parentId].nodes2;
      if($.isArray(p2)) {
        p2.splice(p2.indexOf(item),1);
      }
    }
    delete $rootScope.mapping[item.id];
  }

  $scope.isValidAttr = function(attr){
    var isValid = true;
    angular.forEach(attr, function(a, k){
      if(a.type=="var"){

      }
    });
    return false;
  }

  $scope.sortableOptions = {
      handle: '.handle',
        placeholder: "apps",
        connectWith: ".apps-container"
  };

  $scope.delete = function(data) {
    $rootScope.trackAction("delete");
      data.nodes = [];
  };

  $scope.run = function(useTestCases){
    cleanOutput();
    $rootScope.trackAction("run="+useTestCases);
    
    if(!$scope.validateEverything($scope.program)){
      writer("<i class='fa fa-exclamation-triangle'></i> Existem campos vazios. Preencha os campos com borda vermelha para executar o algoritmo corretamente.", false);
    }else{
      if(useTestCases){

        totalCasesEvaluated = 0;
        totalCasesPassed = 0;

        totalTestCases = $scope.testCases.length;
        angular.forEach($scope.testCases, function(item, key){
          $scope.testCases[key].currentIndex = 0;
        })
        testCases = $scope.testCases;
        var code = "";
        angular.forEach($scope.testCases, function(item, key){
          code += $scope.genCode($scope.program, true, key);
        });
        console.log(code);
        window.eval(code);
  
    
      }else{
        $rootScope.trackAction("run");
        var code = $scope.genCode($scope.program, false, 0);
        window.eval(code);
      }

      $("#valor").unbind('keydown');
      $("#valor").keydown(function( event ) {
        if ( event.which == 13 ) {
          $('#readData').modal('hide');
          var valor = $("#valor").val();
          $("#valor").val("");
          deferred.call(valor);
          event.preventDefault();
        }
      });
      $("#btnOk").unbind('click');
      $("#btnOk").click(function(){
        $('#readData').modal('hide');
        var valor = $("#valor").val();
        $("#valor").val("");
        deferred.call(valor);
      });
    }
  } // $scope.run = function(useTestCases)

  $scope.clearOutput = function(){
    $rootScope.trackAction("cleanOutput");
    $(".output").html("");
  }

  $scope.validateEverything = function(funcs){
    $(".node-with-error").removeClass("node-with-error");
    var ret = true;
    angular.forEach(funcs.functions, function(func, key){
      ret = ret && $scope.validateNode(func.nodes, func.vars);
    });
    return ret;
  }

  $scope.validateNode = function(nodes, vars){
    var ret = true;
    angular.forEach(nodes, function(node, key){
      if (node.type=="write"){
        if(node.variable==""){
          $("#node"+node.id).find(".select").addClass("node-with-error");
          ret = false;
        }
      }
      if(node.type=="read"){
        if(node.variable==""){
          $("#node"+node.id).find(".select").addClass("node-with-error");
          ret = false;
        }
      }
      if(node.type=="for"){
        if(node.forType==1){
          if((node.limitType=="var")&&(node.limit=="")){
            $("#node"+node.id).find(".for1").find(".select").addClass("node-with-error");
            ret = false;
          }
        }
        if(node.forType==2){
          if((node.limitType=="var")&&(node.limit=="")){
            $("#node"+node.id).find(".for1").find(".select").addClass("node-with-error");
            ret = false;
          }
          if(node.using==""){
            $("#node"+node.id).find(".for2").addClass("node-with-error");
            ret = false;
          }  
        }
        if(node.forType==3){
          if((node.limitType=="var")&&(node.limit=="")){
            $("#node"+node.id).find(".for1").find(".select").addClass("node-with-error");
            ret = false;
          }
          if(node.using==""){
            $("#node"+node.id).find(".for2").addClass("node-with-error");
            ret = false;
          }
          if((node.initialType=="var")&&(node.initial=="")){
            ret = false;
          }
          if((node.initialType=="val")&&(node.limitType=="val")&&(node.initial>node.limit)){
            ret = false;
          }
          if((node.stepType=="var")&&(node.step=="")){
            $("#node"+node.id).find(".for3").find(".select").addClass("node-with-error");
            ret = false;
          }
        }
      }
    });
    return ret;
  } // $scope.validateNode = function(nodes, vars)

  $scope.genCode = function(funcs, useTestCases, testCaseIndex){
    var strCode = "var t"+testCaseIndex+" = function(){";
    var i = 0;
    angular.forEach(funcs.functions, function(func, key){
      if(i++==0){
      strCode+= "function "+func.name+"(){";
      angular.forEach(func.vars, function(variable, key){
        if(variable.type=="string"){
          strCode+="var var_"+variable.id+" = \""+variable.initialValue+"\";";
        }else{
          strCode+="var var_"+variable.id+" = "+variable.initialValue+";";
        }
      });
  
      strCode+= 'next(function(){';
      if(useTestCases){
        strCode+='resetTestCase('+testCaseIndex+');';
      }
      //strCode+='/*return deferred;*/';
      strCode+='})';

      // correcao automatica - false
      strCode+=$scope.genNode(useTestCases, func.nodes, func.vars, testCaseIndex);

      if(useTestCases){
        // correcao automatica
        strCode+= '.next(function(){';
        //strCode+='   console.log("OUT "+getOutput());'
        strCode+='   endTest('+testCaseIndex+');'
        if(($scope.testCases.length>testCaseIndex)){
          strCode += 't'+(testCaseIndex+1)+'();';
        }
        strCode+='});';
      }

      strCode+= "}";
      if(func.type=="main"){
        strCode+=func.name+"()";
      }
    }
    });
    strCode+="}; ";
    if(testCaseIndex==0){
      strCode+=" t"+testCaseIndex+"();";
    }
    
    return strCode;
  } // $scope.genCode = function(funcs, useTestCases, testCaseIndex)

  $scope.genNode = function(isEvaluating, nodes, vars, testCaseIndex){
    var strCode = "";
    angular.forEach(nodes, function(node, key){
      if(node.type=="write"){
        if(node.variable!=''){
          var v = $scope.program.functions[$scope.currentFunction].vars[node.variable];
          strCode += ".next(function(){";
    
          if(v.type=="boolean"){
            strCode+="if(var_"+node.variable+"){ writer('Verdadeiro', "+isEvaluating+"); }else{ writer('Falso', "+isEvaluating+"); }";
          }else{
            if(v.type=="float"){
              strCode += "if(isPutDecimalNeeded(parseFloat(var_"+node.variable+"))){";
              strCode += "writer(";
              strCode += "var_"+node.variable;
              strCode += "+'.0',"+isEvaluating+");";
              strCode += "}else{";
              strCode += "writer(";
              strCode += "var_"+node.variable;
              strCode += ","+isEvaluating+");";
              strCode += "}";

            }else{
              strCode += "writer(";
              strCode += "var_"+node.variable;
              strCode += ","+isEvaluating+");";
            }
          }
          strCode += "})";
        }
      }
      if(node.type=="while"){
        // while
        strCode+= '.next(function(){';
        //strCode+= 'var i'+node.id+ ' = 0;';
        strCode+= 'function loop'+node.id+'(){';
        strCode+= '  return next(function(){})'; // apenas para poder encadear
        if(node.nodes.length>0){
          strCode+= $scope.genNode(isEvaluating, node.nodes, vars, testCaseIndex);
        }
        strCode+='  .next(function(){';
        //strCode+='    ++i'+node.id+';';
  
        strCode+='    if('+$scope.genExp(node.exp, 'boolean')+'){';
  
        strCode+='      return loop'+node.id+'();';
        strCode+='    }'
        strCode+='  });';
        strCode+='}';
        strCode+='    if('+$scope.genExp(node.exp, 'boolean')+'){';
        strCode+='return loop'+node.id+'();';
        strCode+='}';
        strCode+='})';
      }
      if(node.type=="for"){
  
        if(node.forType==1){
          // for simples
          strCode+= '.next(function(){';
          strCode+= 'var i'+node.id+ ' = 0;';
          strCode+= 'function loop'+node.id+'(){';
          strCode+= '  return next(function(){})'; // apenas para poder encadear
          if(node.nodes.length>0){
            strCode+= $scope.genNode(isEvaluating, node.nodes, vars, testCaseIndex);
          }
          strCode+='  .next(function(){';
          strCode+='    ++i'+node.id+';';
          if(node.limitType=="val"){
            strCode+='    if(i'+node.id+'<'+node.limit+'){';
          }else{
            strCode+='    if(i'+node.id+'<'+' var_'+node.limit+'){';
          }
          strCode+='      return loop'+node.id+'();';
          strCode+='    }'
          strCode+='  });';
          strCode+='}';
          if(node.limitType=="val"){
            strCode+='    if(i'+node.id+'<'+node.limit+'){';
          }else{
            strCode+='    if(i'+node.id+'<'+' var_'+node.limit+'){';
          }
          strCode+='return loop'+node.id+'();';
          strCode+='}';
          strCode+='})';
        }else if(node.forType==2){
          // for mediano
          strCode+= '.next(function(){';
          strCode+= '  var_'+node.using+ ' = 0;';
          strCode+= 'function loop'+node.id+'(){';
          strCode+= '  return next(function(){})'; // apenas para poder encadear
          if(node.nodes.length>0){
            strCode+= $scope.genNode(isEvaluating, node.nodes, vars, testCaseIndex);
          }
          strCode+='  .next(function(){';
          strCode+='    ++var_'+node.using+';';
          if(node.limitType=="val"){
            strCode+='    if(var_'+node.using+'<'+node.limit+'){';
          }else{
            strCode+='    if(var_'+node.using+'<'+' var_'+node.limit+'){';
          }
          strCode+='      return loop'+node.id+'();';
          strCode+='    }'
          strCode+='  });';
          strCode+='}';
          if(node.limitType=="val"){
            strCode+='    if(var_'+node.using+'<'+node.limit+'){';
          }else{
            strCode+='    if(var_'+node.using+'<'+' var_'+node.limit+'){';
          }
          strCode+='return loop'+node.id+'();';
          strCode+='}';
          strCode+='})';
        }else if(node.forType==3){
          // for hard rs
          strCode+= '.next(function(){';
          if(node.initialType=="val"){
            strCode+= '  var_'+node.using+ ' = '+node.initial+';';
          }else{
            strCode+= '  var_'+node.using+ ' = var_'+node.initial+';';
          }
          strCode+= 'function loop'+node.id+'(){';
          strCode+= '  return next(function(){})'; // apenas para poder encadear
          if(node.nodes.length>0){
            strCode+= $scope.genNode(isEvaluating, node.nodes, vars, testCaseIndex);
          }
          strCode+='  .next(function(){';
          if(node.stepType=="val"){
            strCode+='    var_'+node.using+'+= '+node.step+';';
          }else{
            strCode+='    var_'+node.using+'+= var_'+node.step+';';
          }
    
          if(node.limitType=="val"){
            strCode+='    if(var_'+node.using+'<'+node.limit+'){';
          }else{
            strCode+='    if(var_'+node.using+'<'+' var_'+node.limit+'){';
          }
          strCode+='      return loop'+node.id+'();';
          strCode+='    }'
          strCode+='  });';
          strCode+='}';
          if(node.limitType=="val"){
            strCode+='    if(var_'+node.using+'<'+node.limit+'){';
          }else{
            strCode+='    if(var_'+node.using+'<'+' var_'+node.limit+'){';
          }
          strCode+='return loop'+node.id+'();';
          strCode+='}';
          strCode+='})';
        }
      }
      if(node.type=="attr"){
        if(node.variable!=""){
          strCode+= '.next(function () {';
          strCode+="    var_"+node.variable+"=";
          if(vars[node.variable].type=="int"){
            strCode+="parseInt("+$scope.genExp(node.exp, vars[node.variable].type)+")";
          }else if(vars[node.variable].type=="float"){
            strCode+="parseFloat("+$scope.genExp(node.exp, vars[node.variable].type)+")";
          }else{
            strCode+="      ("+$scope.genExp(node.exp, vars[node.variable].type)+")";
          }
          strCode+="    ;";
          strCode+= '})';
        }
      }
      if(node.type=="read"){
        var v = $scope.program.functions[$scope.currentFunction].vars[node.variable];
  
        if(!isEvaluating){
          strCode+= '.next(function () {';
          strCode+= '    $("#msgRead").html("'+node.message+'");';
          strCode+= '    $("#readData").modal();';
          strCode+= '    $("#valor").focus();';
          strCode+= '    return deferred;';
          strCode+= '}).';
          strCode+= 'next(function(a){';
          strCode+= '    console.log("Valor lido: "+a);';
          strCode+= '/* '+v.type+' */';
        }else{
          strCode+= '.next(function () {';
          strCode+= '    var a = readerInput('+testCaseIndex+');';
        }
        if(v.type=="int"){
          strCode+= "    var_"+node.variable +" = parseInt(a);";  
        }else if(v.type=="float"){
          strCode+= "    var_"+node.variable +" = parseFloat(a); /* pq cai aqui */";
        }else if(v.type=="boolean"){
          // tratar boolean depois
          strCode+= "    var_"+node.variable +" = a;";
        }else if(v.type=="string"){
          // tratar boolean depois
          strCode+= "    var_"+node.variable +" = a;";
        }else{
          strCode+= "    var_"+node.variable +" = a; ";
        }
  
        strCode+= '})';
      }
      if(node.type=="if"){
        strCode+= '.next(function () {';
        strCode+= 'if('+$scope.genExp(node.exp, 'boolean')+'){';
        strCode+= 'return next(function () {})'+$scope.genNode(isEvaluating, node.nodes1, vars, testCaseIndex);
        strCode+= '}else{';
        strCode+= 'return next(function () {})'+$scope.genNode(isEvaluating, node.nodes2, vars, testCaseIndex);
        strCode+= '}';
        strCode+= '})';
      }
    });
    return strCode;
  }
  $scope.genExp = function(exp, type){
    var strCode = "";
    console.log(exp);
    angular.forEach(exp, function(ex, key){
      if(ex.t == "var"){
        strCode+=" var_"+ex.v+" ";
      }else if(ex.t == "val"){
        if(type=="string"){
          strCode+=" \" "+ex.v+"\" ";
        }else{
          strCode+=" "+ex.v+" ";
        }
      }else if(ex.t=="exp"){
        strCode+=" ( "+$scope.genExp(ex.v, type)+" ) ";
      }else if(ex.t=="expB"){
        strCode+=" ( "+$scope.genExp(ex.v, type)+" ) ";
      }else if(ex.t=="op"){
        strCode+= ex.v;
      }else if(ex.t=="opB"){
        strCode+= ex.v;
      }
    });
    return strCode;
  }
  
  $scope.changeForType = function(node, v){
    node.forType +=v;
  }
  $scope.changeForValue = function(node){
    node.isValue = !node.isValue;
    if(!node.isValue){
      node.simpleVariable = "";
    }
    writer(node.isValue, false);
  }
  $scope.childrenVisible = function(node){
    node.isChildrenVisible = !node.isChildrenVisible;
  }

  $scope.add = function(parent, parentId, type, name) {
    $rootScope.trackAction("add;type="+type);
      var newNode = {
        id: $scope.itemCount++,
        type: type,
        name: name,
        nodes: [],
        parent: parentId
        };

    // especifico de cada estrutura
    if(type=="if"){
      newNode.id = "if_"+newNode.id;
      newNode.exp = [/*
        { t: 'expB',
          v: [{"t":"val","v":""},{"t":"opB","v":""},{"t":"val","v":""}]
        }*/
      ];
      newNode.isChildrenVisible = true;
      newNode.nodes1 = [];
      newNode.nodes2 = [];
    }
    if(type=="read"){
      newNode.id = "read_"+newNode.id;
      newNode.message = "Por favor digite um valor:";
      newNode.variable = "";
    }
    if(type=="write"){
      newNode.id = "write_"+newNode.id;
      newNode.variable = "";
    }
    if(type=="while"){
      newNode.id = "while_"+newNode.id;
      newNode.exp = [];
      newNode.isChildrenVisible = true;
      newNode.nodes = [];
    }
    if(type=="for"){
      newNode.id = "for_"+newNode.id;
      newNode.forType = 1; // 1 SIMPLE, 2 +-, 3 COMPLETE

      newNode.initial = 1;
      newNode.initialType = "val";

      newNode.limit = 5;
      newNode.limitType = "val";

      newNode.using = "";

      newNode.step = 1;
      newNode.stepType = "val";

      newNode.isChildrenVisible = true;

      newNode.times = 5;
      newNode.timesType = 5;
      newNode.simple = true;
      newNode.isValue = true;
      newNode.simpleVariable = "";
      newNode.initialValue = 0;
      newNode.endValue = 5;
      newNode.increment = 1;
      newNode.variable = "";
    }

    if(type=="attr"){
      newNode.id = "attr_"+newNode.id;
      newNode.variable = "";
      //newNode.exp = [];
      /*newNode.exp = {
        op1: '',
        op1T : '',
        op: '',
        op2: '',
        op2T: ''
      };*/
      delete newNode.nodes;
      newNode.exp = [];
      newNode.isLocked = false;
    }
    parent.push(newNode);
    $rootScope.mapping[newNode.id] = newNode;
  }; // $scope.add = function(parent, parentId, type, name)

  $scope.save = function(){
    $.post('save.php', { src: JSON.stringify($scope.program) }, function(id) {
      $("body").append("<iframe src='get.php?id=" + id + "' style='display: none;' ></iframe>");
    });
  }

  if(iLMparameters.iLM_PARAM_Assignment!=null){
    //DEBUG: see if exists file under the HTML tag 'iLM_PARAM_Assignment' (that is used here in 'iLMparameters.iLM_PARAM_Assignment')
    //D writer('Entrou: ' + iLMparameters.iLM_PARAM_Assignment + '');

    //TODO NAO esta entrando no GET abaixo
    $.get(iLMparameters.iLM_PARAM_Assignment, function(d){ // load each element 'd'
      //DEBUG: writer('OK: 1 ' + iLMparameters.iLM_PARAM_Assignment + ': ');
      if(d!=null){
        $scope.mapping = d.mapping;
        $scope.program = d.src;
        $scope.testCases = d.testCases;
        $scope.$apply()
        //DEBUG:writer('OK: 2 ' + iLMparameters.iLM_PARAM_Assignment + ': ');// alert(d.src);
        }
      else writer('Erro: nao consegui ler o conteudo de ' + iLMparameters.iLM_PARAM_Assignment);
      }, "json");

    } // if(iLMparameters.iLM_PARAM_Assignment!=null)
  else writer('Erro: parametro \'iLM_PARAM_Assignment\' esta vazio: ' + iLMparameters.iLM_PARAM_Assignment);

  // Help developers
  if (iLMparameters==null) { // iVProgH5 not been used as an iLM?
    writer('There is no address defined to send the answer!');
    writer('There is not source code to load!');
    }
  else {
    if (iLMparameters.iLM_PARAM_Assignment==null || iLMparameters.iLM_PARAM_Assignment.indexOf("http")==-1) {
      strAux = '';
      if (iLMparameters.iLM_PARAM_Assignment!=null && iLMparameters.iLM_PARAM_Assignment!='')
        strAux = 'Try to load the file under ' + iLMparameters.iLM_PARAM_Assignment + '';
      else
        strAux = 'There is not source code to load! (' + iLMparameters.iLM_PARAM_Assignment + ')';
      writer(strAux);
      }
    if (iLMparameters.iLM_PARAM_ServerToGetAnswerURL==null || iLMparameters.iLM_PARAM_ServerToGetAnswerURL=='') {
      strAux = '';
      if (iLMparameters.iLM_PARAM_ServerToGetAnswerURL!=null && iLMparameters.iLM_PARAM_ServerToGetAnswerURL!='')
        strAux = ' (' + iLMparameters.iLM_PARAM_ServerToGetAnswerURL + ')';
      writer('There is no address defined to send the answer!' + strAux);
      }
    }

  //DEBUG x var code = getSource(); // $scope.genCode($scope.program, false, 0);
  //x alert(' src=' + code);

  } // function IvProgCreateCtrl($scope, $rootScope, IvProgSource, $filter)


function IvProgAbertoCtrl($scope){
  //D alert('controllers.js: IvProgAbertoCtrl: isContentEmpty=' + isContentEmpty + ', countEmpty=' + countEmpty + ': ' + $scope);
  $scope.delete = function(data) {
        data.nodes = [];
    };
    $scope.add = function(data) {
        var post = data.nodes.length + 1;
        var newName = data.name + '-' + post;
        data.nodes.push({name: newName,nodes: []});
    };
    $scope.tree = [{name: "Node", nodes: []}];
  }