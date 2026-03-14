<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dom\Mysql;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Estabelece e retorna uma conexão com o banco de dados utilizado pela aplicação.
 *
 * A função tenta conectar ao servidor MySQL configurado (host "db") usando as
 * credenciais internas e seleciona o banco de dados "gym_genesis".
 *
 * @return mysqli|false Objeto mysqli representando a conexão em caso de sucesso,
 *                       ou false em caso de falha na conexão.
 */
function conectar()
{

  $servidor = getenv('DB_HOST');
  $user = getenv('DB_USER');
  $password = getenv('DB_PASSWORD');
  $banco = getenv('DB_NAME');

  return mysqli_connect($servidor, $user, $password, $banco);
}
/**
 * Fecha a conexão com o banco de dados.
 *
 * Observação: apesar de receber um parâmetro $conexao, a implementação atual
 * ignora esse parâmetro e chama conectar() para obter a conexão a ser fechada.
 * Isso pode não ser o comportamento desejado; considere remover a chamada a
 * conectar() e usar a conexão passada em $conexao ou ajustar a assinatura da função.
 *
 * @param mysqli|resource|null $conexao Conexão de banco de dados (atualmente não utilizada).
 * @return void
 */
function desconectar($conexao)
{
  $conexao = conectar();

  mysqli_close($conexao);
}

/**
 * Cadastra um novo usuário na base de dados.
 *
 * Conecta ao banco, gera um hash seguro da senha fornecida e insere um registro
 * na tabela `usuario` com os campos senha (hash), email e tipo_usuario. Fecha a
 * conexão ao final e retorna o resultado da operação.
 *
 * @param string $senha Senha em texto plano que será hasheada com password_hash().
 * @param string $email Endereço de e-mail do usuário a ser cadastrado.
 * @param int    $tipo  Identificador do tipo de usuário (por exemplo: 1 = admin, 2 = cliente).
 *
 * @return array {
 *     Array associativo com o resultado da operação:
 *     @type bool $success Indica se a execução do statement foi bem-sucedida.
 *     @type int|null $id  ID do usuário inserido (valor retornado por mysqli_insert_id) ou null em caso de falha.
 * }
 *
 * @note Esta função depende das funções conectar() e desconectar() para gerenciar a conexão com o banco.
 * @note Comportamento em caso de erro no MySQLi depende da configuração de relatório de erros do MySQLi
 *       (por exemplo, se MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT estiver ativo, exceções podem ser lançadas).
 */
function cadastrarUsuario($senha, $email, $tipo)
{
  $conexao = conectar();
  $senhahash = password_hash($senha, PASSWORD_DEFAULT);
  $sql = 'INSERT INTO usuario (senha, email, tipo_usuario) VALUES (?, ?, ?)';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'ssi',  $senhahash, $email, $tipo);

  $funcionou = mysqli_stmt_execute($comando);
  $id_usuario = mysqli_insert_id($conexao);

  mysqli_stmt_close($comando);
  desconectar($conexao);
  return [
    'success' => $funcionou,
    'id' => $id_usuario
  ];
}

/**
 * Atualiza os dados de um usuário na tabela `usuario`.
 *
 * Esta função abre uma conexão com o banco, verifica se a senha informada é
 * diferente da armazenada e, se for, gera um novo hash usando password_hash().
 * Caso a senha não tenha sido alterada, mantém o hash existente. Em seguida
 * executa um UPDATE na tabela `usuario` para salvar senha, email e tipo.
 *
 * Observações de comportamento:
 * - Usa as funções auxiliares conectar(), loginUsuario(), listarUsuario() e desconectar().
 * - Espera que loginUsuario($email, $senha) retorne um array contendo a chave
 *   'senha' com o valor "diferente" quando a senha fornecida diferir da atual.
 * - Fecha a conexão antes de retornar.
 *
 * @param string $senha      Senha em texto plano (quando for alterada) ou hash existente.
 * @param string $email      Novo e-mail do usuário.
 * @param int    $tipo       Tipo do usuário (valor inteiro conforme esquema do sistema).
 * @param int    $idusuario  Identificador do usuário a ser atualizado.
 *
 * @return bool True se a execução do UPDATE foi bem-sucedida; false caso contrário.
 *
 * @throws mysqli_sql_exception Pode propagar exceções do MySQLi se a extensão estiver configurada para lançar exceções.
 *
 * @see conectar()
 * @see loginUsuario()
 * @see listarUsuario()
 * @see desconectar()
 */
function editarUsuario($senha, $email, $tipo, $idusuario)
{
  $conexao = conectar();
  $resultados = loginUsuario($email, $senha);
  $verify = $resultados['senha'];
  if ($verify == "diferente") {
    $senha = password_hash($senha, PASSWORD_DEFAULT);
  } else {
    $resultado = listarUsuario($idusuario);
    $senha = $resultado['senha'];
  }

  $sql = 'UPDATE usuario SET senha=?, email=?, tipo_usuario=? WHERE idusuario=?';
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, 'ssii', $senha, $email, $tipo, $idusuario);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

/**
 * Remove um usuário da tabela `usuario` pelo seu identificador.
 *
 * A função:
 *  - estabelece uma conexão com o banco de dados chamando conectar();
 *  - prepara uma instrução DELETE com binding de parâmetro para evitar injeção SQL;
 *  - executa a instrução e fecha a conexão chamando desconectar().
 *
 * @param int $idusuario Identificador do usuário a ser deletado (coluna `idusuario`).
 * @return bool Retorna true se a execução da instrução DELETE foi bem-sucedida; false caso ocorra erro na preparação ou execução.
 *
 * Observações:
 * - Depende das funções conectar() e desconectar() estarem implementadas corretamente.
 * - A operação é destrutiva — o registro será removido permanentemente. Considere validações e controles de autorização antes de invocar.
 */
function deletarUsuario($idusuario)
{
  $conexao = conectar();
  $sql = "DELETE FROM usuario WHERE idusuario = ?";
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'i', $idusuario);
  $funcionou = mysqli_stmt_execute($comando);

  desconectar($conexao);
  return $funcionou;
}
/**
 * Autentica um usuário pelo email e senha.
 *
 * Busca na tabela `usuario` um registro cujo email seja igual ao fornecido,
 * compara a senha em texto plano (após trim) com o hash armazenado usando password_verify()
 * e retorna um array associativo com o resultado da autenticação.
 *
 * @param string $email Email do usuário a ser autenticado.
 * @param string $senha Senha em texto plano a ser verificada (será passada por trim() antes de verificar).
 * @return array{
 *   status: bool,      // true se autenticação bem-sucedida, false caso contrário
 *   msg: string,       // mensagem descritiva
 *   id: int|null,      // id do usuário (campo idusuario) quando autenticado; null se não autenticado ou não encontrado
 *   senha: string      // 'igual' quando a senha confere, 'diferente' caso contrário
 * }
 *
 * Observações:
 * - A função assume que as funções conectar() e desconectar() gerenciam uma conexão mysqli.
 * - Usa prepared statement (mysqli_prepare) para evitar SQL injection.
 * - Se nenhum usuário for encontrado, acessar $usuario['senha'] pode gerar avisos; recomenda-se verificar a existência do usuário antes de chamar password_verify().
 */
function loginUsuario($email, $senha)
{
  $conexao = conectar();
  $sql = 'SELECT * FROM usuario WHERE email = ?';
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, 's', $email);
  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);
  $usuario = mysqli_fetch_assoc($resultados);

  if (empty($usuario) || !isset($usuario['senha'])) {
    mysqli_stmt_close($comando);
    desconectar($conexao);
    return [
      "status" => false,
      "msg" => "E-mail não encontrado.",
      "senha" => "inexistente"
    ];
  }

  if (password_verify(trim($senha), $usuario['senha'])) {
    mysqli_stmt_close($comando);
    desconectar($conexao);
    return [
      "status" => true,
      "msg" => "Credenciais corretas.",
      "id" => $usuario['idusuario'],
      "senha" => "igual"
    ];
  } else {
    mysqli_stmt_close($comando);
    desconectar($conexao);
    return [
      "status" => false,
      "msg" => "E-mail ou senha incorretos.",
      "senha" => "diferente"
    ];
  }
}


function cadastrarEndereco($id, $cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $tipo)
{
  $conexao = conectar();
  if ($tipo == 1 or $tipo == 2) {
    $tipoid = "usuario_id";
  } else {
    $tipoid = "funcionario_id";
  }
  $sql = ' INSERT INTO endereco (' . $tipoid . ', cep, rua, numero, complemento, bairro, cidade, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'isssssss', $id, $cep, $rua, $numero, $complemento, $bairro, $cidade, $estado);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function editarEndereco($cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $tipo, $id)
{
  $conexao = conectar();
  if ($tipo == 1 or $tipo == 2) {
    $tipoid = "usuario_id";
  } else {
    $tipoid = "funcionario_id";
  }
  $sql = 'UPDATE endereco SET cep=?, rua=?, numero=?, complemento=?, bairro=?, cidade=?, estado=? WHERE ' . $tipoid . '=?';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'sssssssi', $cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $id);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function deletarEndereco($id, $tipo)
{
  $conexao = conectar();
  if ($tipo == 1) {
    $tipoid = "usuario_id";
  } else {
    $tipoid = "funcionario_id";
  }
  $sql = "DELETE FROM endereco WHERE " . $tipoid . "= ?";
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'i', $id);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function listarEnderecos($tipo)
{
  $conexao = conectar();
  if ($tipo == null) {
    $sql = ' SELECT
    idendereco,
    pf.nome AS nome_usuario,
    f.nome AS nome_funcionario,
    e.cep,
    e.rua,
    e.numero,
    e.complemento,
    e.bairro,
    e.cidade,
    e.estado 
    FROM endereco AS e 
    LEFT JOIN perfil_usuario AS pf ON e.usuario_id = pf.usuario_id 
    LEFT JOIN funcionario AS f ON e.funcionario_id = f.idfuncionario;';
    $comando = mysqli_prepare($conexao, $sql);
  } elseif ($tipo == 1) {
    $sql = ' SELECT
    idendereco,
    pf.nome AS nome_usuario,
    e.cep, e.rua, e.numero,
    e.complemento, e.bairro,
    e.cidade,
    e.estado
    FROM endereco AS e
    LEFT JOIN perfil_usuario AS pf ON e.usuario_id = pf.usuario_id
    WHERE e.usuario_id IS NOT NULL;';
    $comando = mysqli_prepare($conexao, $sql);
  } else {
    $sql = ' SELECT f.nome AS nome_funcionario,
    e.cep,
    e.rua,
    e.numero,
    e.complemento,
    e.bairro,
    e.cidade,
    e.estado FROM endereco AS e
    LEFT JOIN funcionario AS f ON e.funcionario_id = f.idfuncionario
    WHERE e.funcionario_id IS NOT NULL;';
    $comando = mysqli_prepare($conexao, $sql);
  }

  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_enderecos = [];
  while ($endereco = mysqli_fetch_assoc($resultados)) {
    $lista_enderecos[] = $endereco;
  }
  mysqli_stmt_close($comando);

  return $lista_enderecos; // agora retorna array puro
}

function listarEnderecosID($id, $tipo)
{
  $conexao = conectar();
  if ($tipo == 1) {
    $sql = ' SELECT pf.nome AS nome_usuario,
    e.cep,
    e.rua,
    e.numero,
    e.complemento,
    e.bairro,
    e.cidade,
    e.estado
    FROM endereco AS e
    LEFT JOIN perfil_usuario AS pf ON e.usuario_id = pf.usuario_id
    WHERE e.usuario_id=?;';
  } else {
    $sql = ' SELECT f.nome AS nome_funcionario,
    e.cep,
    e.rua,
    e.numero,
    e.complemento,
    e.bairro,
    e.cidade,
    e.estado FROM endereco AS e
    LEFT JOIN funcionario AS f ON e.funcionario_id = f.idfuncionario
    WHERE e.funcionario_id=?;';
  }
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, 'i', $id);
  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_enderecos = [];
  while ($endereco = mysqli_fetch_assoc($resultados)) {
    $lista_enderecos[] = $endereco;
  }
  mysqli_stmt_close($comando);

  return $lista_enderecos;
}

function listarFuncionarios($idfuncionario)
{
  $conexao = conectar();
  if ($idfuncionario != null) {
    $sql = 'SELECT 
            idfuncionario,
            f.nome AS nome_usuario,
            u.email,
            p.telefone,
            f.data_contratacao,
            f.salario,
            f.cargo_id,
            f.usuario_id,
            c.nome AS nome_cargo,
            p.idperfil
            FROM funcionario AS f
            INNER JOIN cargo AS c ON c.idcargo = f.cargo_id
            INNER JOIN usuario AS u on f.usuario_id = u.idusuario
            INNER JOIN perfil_professor AS p ON f.usuario_id = p.usuario_id
            WHERE f.usuario_id = ?;';
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idfuncionario);
  } else {
    $sql = 'SELECT 
            idfuncionario,
            f.nome AS nome_usuario,
            u.email,
            p.telefone,
            f.data_contratacao,
            f.salario,
            f.cargo_id,
            f.usuario_id,
            c.nome AS nome_cargo,
            p.idperfil
            FROM funcionario AS f
            INNER JOIN cargo AS c ON c.idcargo = f.cargo_id
            INNER JOIN usuario AS u on f.usuario_id = u.idusuario
            INNER JOIN perfil_professor AS p ON f.usuario_id = p.usuario_id';
    $comando = mysqli_prepare($conexao, $sql);
  }

  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_funcionarios = [];
  while ($funcionario = mysqli_fetch_assoc($resultados)) {
    $lista_funcionarios[] = $funcionario;
  }
  mysqli_stmt_close($comando);

  // Retorna array diretamente
  return $lista_funcionarios;
}

/**
 * Undocumented function
 *
 * @param [type] $idfuncionario
 * @return void
 */
function deletarFuncionario($idfuncionario)
{
  $conexao = conectar();
  $sql = " DELETE FROM funcionario WHERE idfuncionario = ?";
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'i', $idfuncionario);
  $funcionou = mysqli_stmt_execute($comando);

  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function editarCargo($idcargo, $nome, $descricao)
{
  $conexao = conectar();

  $sql = 'UPDATE cargo SET nome=?, descricao=? WHERE idcargo=?';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'ssi', $nome, $descricao, $idcargo);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function deletarCargo($idcargo): bool
{
  $conexao = conectar();

  $sql = "DELETE FROM cargo WHERE idcargo = ?";
  $comando = mysqli_prepare($conexao, $sql);

  if (!$comando) {
    echo "Erro na preparação: " . mysqli_error($conexao);
    return false;
  }

  mysqli_stmt_bind_param($comando, "i", $idcargo);
  $funcionou = mysqli_stmt_execute($comando);


  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function cadastrarAssinatura($data_inicio, $data_fim, $idplano, $idusuario)
{
  $conexao = conectar();

  $sql = 'INSERT INTO assinatura (data_inicio, data_fim, usuario_id, plano_id) VALUES (?, ?, ?, ?)';
  $comando = mysqli_prepare($conexao, $sql);

  // CORRETO: $idusuario vem antes de $idplano
  mysqli_stmt_bind_param($comando, 'ssii', $data_inicio, $data_fim, $idusuario, $idplano);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function renovarAssinatura($idusuario, $data_inicio, $data_fim)
{
  $conexao = conectar();

  $sql = 'UPDATE assinatura SET data_inicio=?, data_fim=? WHERE usuario_id=?';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'ssi', $data_inicio, $data_fim, $idusuario);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function deletarAssinatura($idusuario)
{
  $conexao = conectar();
  $sql = "DELETE FROM assinatura WHERE usuario_id=?";
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'i', $idusuario);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function cadastrarPlano($tipo, $duracao)
{
  $conexao = conectar();

  $sql = 'INSERT INTO plano (tipo, duracao) VALUES (?, ?)';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'ss', $tipo, $duracao);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function editarPlano($idplano, $tipo, $duracao)
{
  $conexao = conectar();

  $sql = 'UPDATE plano SET tipo=?, duracao=? WHERE idplano=?';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'ssi', $tipo, $duracao, $idplano);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function listarPlanos($idplano)
{
  $conexao = conectar();

  if ($idplano != null) {
    $sql = 'SELECT * FROM plano WHERE idplano = ?';
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idplano);
  } else {
    $sql = 'SELECT * FROM plano';
    $comando = mysqli_prepare($conexao, $sql);
  }

  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_planos = [];
  while ($plano = mysqli_fetch_assoc($resultados)) {
    $lista_planos[] = $plano;
  }

  mysqli_stmt_close($comando);

  return $lista_planos; // agora retorna array puro

}

function editarMetaUsuario($idmeta, $idusuario, $descricao, $data_inicio, $data_limite, $status)
{
  $conexao = conectar();

  $sql = 'UPDATE meta_usuario 
        SET descricao = ?, data_inicio = ?, data_limite = ?, status = ? 
        WHERE idmeta = ? AND usuario_id = ?';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'ssssii', $descricao, $data_inicio, $data_limite, $status, $idmeta, $idusuario);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function deletarMetaUsuario($idmeta)
{
  $conexao = conectar();

  $sql = 'DELETE FROM meta_usuario WHERE idmeta=?';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'i', $idmeta);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function cadastrarTreino($tipo, $horario, $descricao, $idfuncionario)
{
  $conexao = conectar();

  $sql = 'INSERT INTO treino (tipo, horario, descricao, funcionario_id) VALUES (?, ?, ?, ?)';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'sssi', $tipo, $horario, $descricao, $idfuncionario);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function editarTreino($tipo, $horario, $descricao, $idtreino)
{
  $conexao = conectar();

  $sql = ' UPDATE treino SET tipo=?, horario=?, descricao=? WHERE idtreino=?';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'sssi', $tipo, $horario, $descricao, $idtreino);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function deletarAvaliacaoFisica($idusuario)
{
  $conexao = conectar();
  $sql = " DELETE FROM avaliacao_fisica WHERE usuario_id=?";
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'i', $idusuario);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function editarDieta($descricao, $data_inicio, $data_fim, $idusuario, $iddieta)
{
  $conexao = conectar();

  $sql = 'UPDATE dieta SET descricao=?, data_inicio=?, data_fim=?, usuario_id=? WHERE iddieta=?';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'sssii', $descricao, $data_inicio, $data_fim, $idusuario, $iddieta);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function deletarDieta($idusuario)
{
  $conexao = conectar();
  $sql = "DELETE FROM dieta WHERE usuario_id=?";
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'i', $idusuario);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function listarDietas($iddieta)
{
  $conexao = conectar();
  if ($iddieta != null) {
    $sql = 'SELECT
    iddieta,
    pf.usuario_id,
    pf.nome AS nome_usuario,
    d.descricao,
    d.data_inicio,
    d.data_fim
    FROM dieta AS d
    JOIN perfil_usuario AS pf ON d.usuario_id = pf.usuario_id
    WHERE d.iddieta=?';
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $iddieta);
  } else {
    $sql = ' SELECT
    iddieta,
    pf.usuario_id,
    pf.nome AS nome_usuario,
    d.descricao,
    d.data_inicio,
    d.data_fim
    FROM dieta AS d
    JOIN perfil_usuario AS pf ON d.usuario_id = pf.usuario_id';
    $comando = mysqli_prepare($conexao, $sql);
  }
  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_dietas = [];
  while ($dieta = mysqli_fetch_assoc($resultados)) {
    $lista_dietas[] = $dieta;
  }
  mysqli_stmt_close($comando);

  return $lista_dietas;
}

function listarDietasUsuario($idusuario)
{
  $conexao = conectar();
  if ($idusuario != null) {
    $sql = ' SELECT
    pf.nome AS nome_usuario,
    d.descricao,
    d.data_ini,
    d.data_fim
    FROM dieta AS d
    JOIN perfil_usuario AS pf ON d.usuario_id = pf.usuario_id
    WHERE d.usuario_id=?';
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idusuario);
  } else {
    $sql = ' SELECT
    pf.nome AS nome_usuario,
    d.descricao,
    d.data_inicio,
    d.data_fim
    FROM dieta AS d
    JOIN perfil_usuario AS pf ON d.usuario_id = pf.usuario_id';
    $comando = mysqli_prepare($conexao, $sql);
  }
  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_dietas = [];
  while ($dieta = mysqli_fetch_assoc($resultados)) {
    $lista_dietas[] = $dieta;
  }
  mysqli_stmt_close($comando);

  return $lista_dietas;
}

function deletarTreinoExercicio($idtreino2)
{
  $conexao = conectar();
  $sql = " DELETE FROM treino_exercicio WHERE idtreino2=?";
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'i', $idtreino2);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function listarTreinoExercicio($idtreino2)
{
  $conexao = conectar();
  if ($idtreino2 != null) {
    $sql = ' SELECT
    idtreino2,
    e.nome,
    t.tipo,
    te.series,
    te.repeticoes,
    te.carga,
    te.intervalo_segundos
    FROM treino_exercicio AS te
    JOIN treino AS t ON te.treino_id = t.idtreino
    JOIN exercicio AS e ON te.exercicio_id = e.idexercicio
    WHERE te.idtreino2=?';
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idtreino2);
  } else {
    $sql = ' SELECT
    idtreino2,
    e.nome,
    t.tipo,
    te.series,
    te.repeticoes,
    te.carga,
    te.intervalo_segundos
    FROM treino_exercicio AS te
    JOIN treino AS t ON te.treino_id = t.idtreino
    JOIN exercicio AS e ON te.exercicio_id = e.idexercicio';
    $comando = mysqli_prepare($conexao, $sql);
  }
  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_treinos = [];
  while ($treino = mysqli_fetch_assoc($resultados)) {
    $lista_treinos[] = $treino;
  }
  mysqli_stmt_close($comando);

  return $lista_treinos;
}

function listarTreinoExercicioTreino($idtreino)
{
  $conexao = conectar();
  $sql = ' SELECT
  idtreino,
  e.nome,
  t.tipo,
  te.series,
  te.repeticoes,
  te.carga,
  te.intervalo_segundos
  FROM treino_exercicio AS te
  JOIN treino AS t ON te.treino_id = t.idtreino
  JOIN exercicio AS e ON te.exercicio_id = e.idexercicio
  WHERE t.idtreino=?';
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, 'i', $idtreino);
  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_treinos = [];
  while ($treino = mysqli_fetch_assoc($resultados)) {
    $lista_treinos[] = $treino;
  }
  mysqli_stmt_close($comando);

  return $lista_treinos;
}

function editarExercicio($idexercicio, $nome, $grupo_muscular, $descricao, $video_url)
{
  $conexao = conectar();

  $sql = ' UPDATE exercicio SET nome=?, grupo_muscular=?, descricao=?, video_url=? WHERE idexercicio=?';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'ssssi', $nome, $grupo_muscular, $descricao, $video_url, $idexercicio);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function deletarRefeicao($idrefeicao)
{
  $conexao = conectar();
  $sql = " DELETE FROM refeicao WHERE idrefeicao=?";
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'i', $idrefeicao);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function deletarProduto($idproduto)
{
  $conexao = conectar();
  $sql = " DELETE FROM produto WHERE idproduto=?";
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'i', $idproduto);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function gerenciarVencimento($idusuario, $data_fim)
{
  $conexao = conectar();

  $sql = ' UPDATE assinatura SET data_fim=? WHERE usuario_id=?';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'si', $data_fim, $idusuario);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function listarCupomDesconto($idcupom)
{
  $conexao = conectar();
  if ($idcupom != null) {
    $sql = 'SELECT * FROM cupom_desconto WHERE idcupom=?';
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idcupom);
  } else {
    $sql = 'SELECT * FROM cupom_desconto';
    $comando = mysqli_prepare($conexao, $sql);
  }
  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_cupons = [];
  while ($cupom = mysqli_fetch_assoc($resultados)) {
    $lista_cupons[] = $cupom;
  }
  mysqli_stmt_close($comando);

  return $lista_cupons;
}

function editarCategoriaProduto($idcategoria, $nome, $descricao)
{
  $conexao = conectar();

  $sql = ' UPDATE categoria_produto SET nome=?, descricao=? WHERE usuario_id=?';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'si', $nome, $descricao, $idcategoria);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function listarPedidos($idpedido)
{
  $conexao = conectar();
  if ($idpedido != null) {
    $sql = ' SELECT
    idpedido,
    pf.usuario_id,
    pf.nome AS nome_usuario,
    p.data_pedido,
    p.pagamento_id,
    pa.metodo,
    p.status,
    pa.valor
    FROM pedido AS p
    JOIN perfil_usuario AS pf ON p.usuario_id = pf.usuario_id
    JOIN pagamento AS pa ON p.pagamento_id = pa.idpagamento
    WHERE p.idpedido=?';
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idpedido);
  } else {
    $sql = ' SELECT
    idpedido,
    pf.usuario_id,
    pf.nome AS nome_usuario,
    p.data_pedido,
    p.pagamento_id,
        pa.metodo,

    p.status,
    pa.valor
    FROM pedido AS p
    JOIN perfil_usuario AS pf ON p.usuario_id = pf.usuario_id
    JOIN pagamento AS pa ON p.pagamento_id = pa.idpagamento';
    $comando = mysqli_prepare($conexao, $sql);
  }
  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_pedidos = [];
  while ($pedido = mysqli_fetch_assoc($resultados)) {
    $lista_pedidos[] = $pedido;
  }
  mysqli_stmt_close($comando);

  return $lista_pedidos;
}

function listarProdutos($idproduto)
{
  $conexao = conectar();
  if ($idproduto != null) {
    $sql = 'SELECT * FROM produto WHERE idproduto=?';
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idproduto);
  } else {
    $sql = 'SELECT * FROM produto';
    $comando = mysqli_prepare($conexao, $sql);
  }
  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_produtos = [];
  while ($produto = mysqli_fetch_assoc($resultados)) {
    $lista_produtos[] = $produto;
  }
  mysqli_stmt_close($comando);
  $lista_produtoss = array_values($lista_produtos);
  return $lista_produtoss;
}

function editarAulaAgendada($data_aula, $dia_semana, $hora_inicio, $hora_fim, $idtreino, $funcionario_id, $idaula)
{
  $conexao = conectar();

  $sql = ' UPDATE aula_agendada SET data_aula=?, dia_semana=?, hora_inicio=?, hora_fim=?, treino_id=?, funcionario_id=? WHERE idaula=?';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'ssssiii', $data_aula, $dia_semana, $hora_inicio, $hora_fim, $idtreino, $funcionario_id, $idaula);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function cadastrarRefeicao($iddieta, $tipo, $horario)
{
  $conexao = conectar();

  $sql = 'INSERT INTO refeicao (dieta_id, tipo, horario) VALUES (?, ?, ?)';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'iss', $iddieta, $tipo, $horario);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function cadastrarCupomDesconto($codigo, $percentual_desconto, $valor_desconto, $data_validade, $quantidade_uso, $tipo)
{
  $conexao = conectar();

  $sql = 'INSERT INTO cupom_desconto (codigo, percentual_desconto, valor_desconto, data_validade, quantidade_uso, tipo) VALUES (?, ?, ?, ?, ?, ?)';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'sddsis', $codigo, $percentual_desconto, $valor_desconto, $data_validade, $quantidade_uso, $tipo);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function cadastrarPedido($idusuario, $data_pedido, $status, $idpagamento)
{
  $conexao = conectar();

  $sql = 'INSERT INTO pedido (usuario_id, data_pedido, status, pagamento_id) VALUES (?, ?, ?, ?)';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'issi', $idusuario, $data_pedido, $status, $idpagamento);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function cadastrarRespostaForum($mensagem, $idusuario, $idtopico)
{
  $conexao = conectar();

  $sql = 'INSERT INTO resposta_forum (mensagem, usuario_id, forum_id) VALUES (?, ?, ?)';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'sii', $mensagem, $idusuario, $idtopico);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function listarForum($idtopico)
{
  $conexao = conectar();
  if ($idtopico != null) {
    $sql = ' SELECT
    f.idtopico,
    pf.nome AS nome_usuario,
    f.titulo,
    f.descricao,
    f.data_criacao,
    pf.usuario_id
    FROM forum AS f
    JOIN perfil_usuario AS pf ON f.usuario_id = pf.usuario_id
    WHERE f.idtopico=?;';
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idtopico);
  } else {
    $sql = 'SELECT
    f.idtopico,
    f.usuario_id,
    pf.nome AS nome_usuario,
    f.titulo,
    f.descricao,
    f.data_criacao,
    pf.usuario_id
    FROM forum AS f
    INNER JOIN perfil_usuario AS pf ON f.usuario_id = pf.usuario_id';
    $comando = mysqli_prepare($conexao, $sql);
  }
  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_foruns = [];
  while ($forum = mysqli_fetch_assoc($resultados)) {
    $lista_foruns[] = $forum;
  }
  mysqli_stmt_close($comando);

  return $lista_foruns;
}

function cadastrarForum($titulo, $descricao, $usuario_id)
{
  $conexao = conectar();

  $sql = 'INSERT INTO forum (titulo, descricao, usuario_id) VALUES (?, ?, ?)';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'ssi', $titulo, $descricao, $usuario_id);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function listarHistoricoTreino($idhistorico)
{
  $conexao = conectar();
  if ($idhistorico != null) {
    $sql = 'SELECT
    ht.idhistorico,
    ht.usuario_id,
    pf.nome AS nome_usuario,
    t.tipo,
    ht.treino_id,
    ht.data_execucao,
    ht.observacoes
    FROM historico_treino AS ht
    JOIN perfil_usuario AS pf ON ht.usuario_id = pf.usuario_id
    JOIN treino AS t ON ht.treino_id = t.idtreino
    WHERE idhistorico=?';
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idhistorico);
  } else {
    $sql = 'SELECT
    ht.idhistorico,
    ht.usuario_id,
    pf.nome AS nome_usuario,
    t.tipo,
    ht.treino_id,
    ht.data_execucao,
    ht.observacoes
    FROM historico_treino AS ht
    JOIN perfil_usuario AS pf ON ht.usuario_id = pf.usuario_id
    JOIN treino AS t ON ht.treino_id = t.idtreino';
    $comando = mysqli_prepare($conexao, $sql);
  }
  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_historicos = [];
  while ($historico = mysqli_fetch_assoc($resultados)) {
    $lista_historicos[] = $historico;
  }
  mysqli_stmt_close($comando);

  return $lista_historicos;
}

function editarHistoricoTreino($idhistorico, $data_execucao, $observacoes)
{
  $conexao = conectar();

  $sql = ' UPDATE historico_treino SET data_execucao=?, observacoes=? WHERE idhistorico=?';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'ssi', $data_execucao, $observacoes, $idhistorico);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function editarForum($idtopico, $titulo, $descricao)
{
  $conexao = conectar();

  $sql = ' UPDATE forum SET titulo=?, descricao=? WHERE idtopico=?';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'ssi', $titulo, $descricao, $idtopico);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function editarCupomDesconto($idcupom, $codigo, $percentual_desconto, $valor_desconto, $data_validade, $quantidade_uso, $tipo)
{
  $conexao = conectar();

  $sql = ' UPDATE cupom_desconto SET codigo=?, percentual_desconto=?, valor_desconto=?, data_validade=?, quantidade_uso=?, tipo=? WHERE idcupom=?';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'sddsisi', $codigo, $percentual_desconto, $valor_desconto, $data_validade, $quantidade_uso, $tipo, $idcupom);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function listarPagamentos($idpagamento)
{
  $conexao = conectar();
  if ($idpagamento != null) {
    $sql = 'SELECT * FROM pagamento WHERE idpagamento=?';
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idpagamento);
  } else {
    $sql = 'SELECT * FROM pagamento';
    $comando = mysqli_prepare($conexao, $sql);
  }
  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_pagamentos = [];
  while ($pagamento = mysqli_fetch_assoc($resultados)) {
    $lista_pagamentos[] = $pagamento;
  }
  mysqli_stmt_close($comando);

  return $lista_pagamentos;
}

function editarPagamento($idpagamento, $valor, $data_pagamento, $metodo, $status)
{
  $conexao = conectar();

  $sql = ' UPDATE pagamento SET valor=?, data_pagamento=?, metodo=?, status=? WHERE idpagamento=?';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'dsssi', $valor, $data_pagamento, $metodo, $status, $idpagamento);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function editarDietaAlimentar($idalimento, $idrefeicao, $quantidade, $observacao)
{
  $conexao = conectar();

  $sql = ' UPDATE dieta_alimentar SET quantidade=?, observacao=? WHERE alimento_id=? and refeicao_id=?';
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'dsii', $quantidade, $observacao, $idalimento, $idrefeicao);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function enviarCodigoSeguranca($emails, $idusuario)
{
  // Garante que $emails seja sempre um array
  if (!is_array($emails)) {
    $emails = [$emails];
  }

  $resultados = [];

  foreach ($emails as $email_destinatario) {
    $codigo = random_int(100000, 999999);
    $expiracao = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    $expiracao_msg = date('d/m/Y H:i:s', strtotime('+10 minutes'));

    $email = new PHPMailer(true);

    try {
      // Configurações do SMTP
      $email->isSMTP();
      $email->Host = 'smtp.gmail.com';
      $email->SMTPAuth = true;
      $email->Username = 'smtpemaile@gmail.com'; // Seu Gmail
      $email->Password = 'xjqc orkg ckls fant';  // Senha de app
      $email->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $email->Port = 587;

      // Remetente e destinatário
      $email->setFrom('smtpemaile@gmail.com', 'Academia Gym Genesis');
      $email->addAddress($email_destinatario);

      // Conteúdo do e-mail
      $email->isHTML(true);
      $email->Subject = 'Recuperação de Senha';
      $email->Body = '
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="UTF-8" />
                <style>
                    body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #e0f7fa, #e1bee7); padding: 20px; }
                    .container { background-color: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); max-width: 400px; margin: auto; text-align: center; }
                    .codigo { font-size: 32px; font-weight: bold; color: #2e7d32; background-colortrocar_senha.php: #e8f5e9; padding: 15px; border-radius: 8px; margin: 20px 0; letter-spacing: 2px; }
                    .expiracao { font-size: 13px; color: #888; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h2>Recuperação de Senha</h2>
                    <p>Seu código de segurança:</p>
                    <div class="codigo">' . $codigo . '</div>
                    <p class="expiracao">Expira em: ' . $expiracao_msg . '</p>
                </div>
            </body>
            </html>
            ';

      $email->send();

      // Inserir no banco de dados
      $conexao = conectar();
      $sql = 'INSERT INTO recuperacao_senha (codigo, tempo_expiracao, usuario_id) VALUES (?, ?, ?)';
      $comando = mysqli_prepare($conexao, $sql);
      mysqli_stmt_bind_param($comando, 'isi', $codigo, $expiracao, $idusuario);
      mysqli_stmt_execute($comando);
      mysqli_stmt_close($comando);
      desconectar($conexao);

      $resultados[] = [
        'email' => $email_destinatario,
        'codigo' => $codigo,
        'expira' => $expiracao,
        'status' => 'enviado'
      ];
    } catch (Exception $e) {
      $resultados[] = [
        'email' => $email_destinatario,
        'status' => 'erro',
        'mensagem' => $email->ErrorInfo
      ];
    }
  }

  return $resultados;
}


function VerificarCodigo($codigoInserido, $idusuario)
{
  $conexao = conectar();

  $sql = 'SELECT codigo, tempo_expiracao FROM recuperacao_senha WHERE usuario_id = ? ORDER BY tempo_expiracao DESC LIMIT 1';
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, 'i', $idusuario);
  mysqli_stmt_execute($comando);

  $resultados = mysqli_stmt_get_result($comando);
  $resultado = mysqli_fetch_assoc($resultados);

  mysqli_stmt_close($comando);
  desconectar($conexao);

  if (!$resultado) {
    return false; // Usuário não tem código de recuperação
  }

  $codigoArmazenado = $resultado['codigo'];
  $expiracaoArmazenada = $resultado['tempo_expiracao'];

  if ($codigoInserido !== $codigoArmazenado) {
    $resposta =  "codigo inserido esta incorreto"; // Código incorreto
    return $resposta;
  }

  if (strtotime($expiracaoArmazenada) <= time()) {
    $resposta =  "codigo inserido expirou";
    return $resposta; // Código expirado
  }

  return true; // Código correto e válido
}


function aplicarDesconto($idpagamento, $idcupom)
{
  $conexao = conectar();
  $sql = 'SELECT percentual_desconto, valor_desconto, quantidade_uso FROM cupom_desconto WHERE idcupom=?';
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, 'i', $idcupom);
  mysqli_stmt_execute($comando);
  $valor_desconto = mysqli_stmt_get_result($comando);
  $resultado = mysqli_fetch_assoc($valor_desconto);
  $sql2 = 'SELECT valor FROM pagamento WHERE idpagamento=?';
  $comando2 = mysqli_prepare($conexao, $sql2);
  mysqli_stmt_bind_param($comando2, 'i', $idpagamento);
  mysqli_stmt_execute($comando2);
  $valor_da_compra = mysqli_stmt_get_result($comando2);
  $valor2 = mysqli_fetch_assoc($valor_da_compra);
  $valor = (float)$valor2['valor'];

  if ($resultado['percentual_desconto'] != null) {
    $desconto = (float)$resultado['percentual_desconto'];
    $valor_com_desconto = $valor - $valor * ($desconto / 100);
  } elseif ($resultado['valor_desconto'] != null) {
    $desconto = (float)$resultado['valor_desconto'];
    $valor_com_desconto = $valor - $desconto;
  }
  $sql3 = 'UPDATE pagamento SET valor=? WHERE idpagamento=?';
  $comando3 = mysqli_prepare($conexao, $sql3);
  mysqli_stmt_bind_param($comando3, 'di', $valor_com_desconto, $idpagamento);
  mysqli_stmt_execute($comando3);
  $usos_restantes = (int)$resultado['quantidade_uso'];
  $sql4 = 'UPDATE cupom_desconto SET quantidade_uso=? WHERE idcupom=?';
  $comando4 = mysqli_prepare($conexao, $sql4);
  mysqli_stmt_bind_param($comando4, 'ii', $usos_restantes, $idcupom);
  mysqli_stmt_execute($comando4);
  mysqli_stmt_close($comando);
  mysqli_stmt_close($comando2);
  mysqli_stmt_close($comando3);
  mysqli_stmt_close($comando4);
}

function ajustarDataHora($DataeHora)
{

  $DataeHoraUTC = strtotime($DataeHora . ' UTC');
  $DataeHoraLocal = $DataeHoraUTC - (3 * 3600);
  $DataeHoraConvertido = gmdate('d-m-Y H:i:s', $DataeHoraLocal);
  return $DataeHoraConvertido;
}

function uploadImagem($foto)
{
  $resposta = "";
  $uploadOk = 1;
  $target_dir = __DIR__ . '/../public/uploads/';

  if (!isset($foto) || !isset($foto["tmp_name"]) || empty($foto["tmp_name"])) {
    return ['erro' => "Nenhum arquivo foi enviado."];
  }

  $check = getimagesize($foto["tmp_name"]);
  if ($check === false) {
    return ['erro' => "O arquivo não é uma imagem."];
  }

  $extensao = strtolower(pathinfo($foto["name"], PATHINFO_EXTENSION));
  $novoNome = uniqid('img_') . '.' . $extensao;
  $caminho = $target_dir . $novoNome;

  if (move_uploaded_file($foto["tmp_name"], $caminho)) {
    return $novoNome;
  } else {
    return ['erro' => "Falha ao mover o arquivo."];
  }
}


function mostrarImagem($caminhoImagem)
{
  if (!file_exists($caminhoImagem)) {
    http_response_code(404);
    echo "Imagem não encontrada.";
    exit;
  }

  header("Content-Type: " . mime_content_type($caminhoImagem));
  header("Content-Length: " . filesize($caminhoImagem));
  readfile($caminhoImagem);
  exit;
}

function deletarPagamento($idpagamento)
{
  $conexao = conectar();
  $sql = " DELETE FROM pagamento WHERE idpagamento=?";
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'i', $idpagamento);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}
///////////////////////////////////////////////////////////////////////////////////////// ultimo que o jose fez//////////////////////////////////////////////////////////////////////////////////////


function cadastrarAvaliacaoFisica($peso, $altura, $imc, $percentual_gordura, $data_avaliacao, $idusuario)
{
  $conexao = conectar();

  $sql = 'INSERT INTO avaliacao_fisica (peso, altura, imc, percentual_gordura, data_avaliacao, usuario_id) VALUES (?, ?, ?, ?, ?, ?)';

  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, "ddddsi", $peso, $altura, $imc, $percentual_gordura, $data_avaliacao, $idusuario);

  $funcionou = mysqli_stmt_execute($comando);

  mysqli_stmt_close($comando);

  desconectar($conexao);

  return $funcionou;
}


function cadastrarPagamentoDetalhe($pagamento_id, $tipo, $bandeira_cartao, $ultimos_digitos, $codigo_pix, $linha_digitavel_boleto): bool
{
  $conexao = conectar();

  // Ajusta os campos de acordo com o tipo de pagamento
  switch ($tipo) {
    case 'cartao':
      $codigo_pix = null;
      $linha_digitavel_boleto = null;
      break;
    case 'pix':
      $bandeira_cartao = null;
      $ultimos_digitos = null;
      $linha_digitavel_boleto = null;
      break;
    case 'boleto':
      $bandeira_cartao = null;
      $ultimos_digitos = null;
      $codigo_pix = null;
      break;
  }

  $sql = 'INSERT INTO pagamento_detalhe 
            (pagamento_id, tipo, bandeira_cartao, ultimos_digitos, codigo_pix, linha_digitavel_boleto) 
            VALUES (?, ?, ?, ?, ?, ?)';

  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param(
    $comando,
    "isssss",
    $pagamento_id,
    $tipo,
    $bandeira_cartao,
    $ultimos_digitos,
    $codigo_pix,
    $linha_digitavel_boleto
  );

  $funcionou = mysqli_stmt_execute($comando);

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function editarTreinoExercicio($idtreino2, $treino_id, $exercicio_id, $series, $repeticoes, $carga, $intervalo_segundos)
{
  $conexao = conectar();

  $sql = "UPDATE treino_exercicio 
          SET treino_id = ?, exercicio_id = ?, series = ?, repeticoes = ?, carga = ?, intervalo_segundos = ? 
          WHERE idtreino2 = ?";

  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "iiiidii", $treino_id, $exercicio_id, $series, $repeticoes, $carga, $intervalo_segundos, $idtreino2);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function deletarForum($idtopico)
{
  $conexao = conectar();
  $sql = "DELETE FROM forum WHERE idtopico=?";
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'i', $idtopico);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function listarExercicio($idexercicio)
{
  $conexao = conectar();

  if ($idexercicio !== null) {
    $sql = " SELECT * FROM exercicio WHERE $idexercicio = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, "i", $idexercicio);
  } else {
    $sql = " SELECT * FROM exercicio";
    $comando = mysqli_prepare($conexao, $sql);
  }

  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_exercicios = [];
  while ($exercicio = mysqli_fetch_assoc($resultados)) {
    $lista_exercicios[] = $exercicio;
  }

  mysqli_stmt_close($comando);

  return $lista_exercicios;
}

function cadastrarTreinoExercicio($treino_id, $exercicio_id, $series, $repeticoes, $carga, $intervalo_segundos)
{
  $conexao = conectar();
  $sql = " INSERT INTO treino_exercicio (treino_id, exercicio_id, series, repeticoes, carga, intervalo_segundos) VALUES (?, ?, ?, ?, ?, ?)";
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, "iiiidi", $treino_id, $exercicio_id, $series, $repeticoes, $carga, $intervalo_segundos);
  $funcionou = mysqli_stmt_execute($comando);

  $idInserido = null;
  if ($funcionou) {
    $idInserido = mysqli_insert_id($conexao);
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $idInserido; // retorna o ID inserido, ou null se não funcionou
}

function deletarRespostaForum($idresposta)
{
  $conexao = conectar();
  $sql = "DELETE FROM resposta_forum WHERE idresposta =?";
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, 'i', $idresposta);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function editarAvaliacaoFisica($idavaliacao, $peso, $altura, $imc, $percentual_gordura, $data_avaliacao, $usuario_id)
{
  $conexao = conectar();

  $sql = "UPDATE avaliacao_fisica 
          SET peso = ?, altura = ?, imc = ?, percentual_gordura = ?, data_avaliacao = ?, usuario_id = ? 
          WHERE idavaliacao = ?";

  $comando = mysqli_prepare($conexao, $sql);

  if (!$comando) {
    echo "Erro na preparação: " . mysqli_error($conexao);
    return false;
  }

  // Ordem correta dos parâmetros
  mysqli_stmt_bind_param($comando, "ddddssi", $peso, $altura, $imc, $percentual_gordura, $data_avaliacao, $usuario_id, $idavaliacao);

  $funcionou = mysqli_stmt_execute($comando);

  if (!$funcionou) {
    echo "Erro na execução: " . mysqli_stmt_error($comando);
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function editarItemPedido($pedido_id, $produto_id, $quantidade, $preco_unitario): bool
{
  $conexao = conectar();

  $sql = "UPDATE item_pedido 
            SET quantidade = ?, preco_unitario = ?
            WHERE pedido_id = ? AND produto_id = ?";

  $comando = mysqli_prepare($conexao, $sql);

  if (!$comando) {
    echo "Erro na preparação: " . mysqli_error($conexao);
    return false;
  }

  // "i" para int, "d" para double
  mysqli_stmt_bind_param($comando, "idii", $quantidade, $preco_unitario, $pedido_id, $produto_id);

  $funcionou = mysqli_stmt_execute($comando);

  if (!$funcionou) {
    echo "Erro na execução: " . mysqli_stmt_error($comando);
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function editarFuncionario($idfuncionario, $nome, $data_contratacao, $salario, $cargo_id, $usuario_id)
{
  $conexao = conectar();

  $sql = "UPDATE funcionario 
            SET nome = ?, data_contratacao = ?, salario = ?, cargo_id = ?, usuario_id=?
            WHERE idfuncionario = ?";

  $comando = mysqli_prepare($conexao, $sql);

  if (!$comando) {
    echo "Erro na preparação: " . mysqli_error($conexao);
    return false;
  }

  // Correção aqui:
  mysqli_stmt_bind_param($comando, "sssiii", $nome, $data_contratacao, $salario, $cargo_id, $usuario_id, $idfuncionario);

  $funcionou = mysqli_stmt_execute($comando);

  if (!$funcionou) {
    echo "Erro na execução: " . mysqli_stmt_error($comando);
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function listarTreino($idtreino)
{
  $conexao = conectar();

  if ($idtreino != null) {
    $sql = " SELECT 
    idtreino,
    pf.nome AS nome_usuario,
    t.tipo,
    t.horario,
    t.descricao
    FROM treino as t
    JOIN perfil_usuario AS pf ON pf.usuario_id = t.funcionario_id 
    WHERE idtreino = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, "i", $idtreino);
  } else {
    $sql = " SELECT
    idtreino,
    pf.nome AS nome_usuario,
    t.tipo,
    t.horario,
    t.descricao
    FROM treino as t
    JOIN perfil_usuario AS pf ON pf.usuario_id = t.funcionario_id
    
    
    ";
    $comando = mysqli_prepare($conexao, $sql);
  }

  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_treinos = [];
  while ($treino = mysqli_fetch_assoc($resultados)) {
    $lista_treinos[] = $treino;
  }

  mysqli_stmt_close($comando);
  return $lista_treinos;
}

function listarTreinoUsuario($idusuario)
{
  $conexao = conectar();

  $sql = " SELECT 
    t.idtreino,
    t.tipo,
    t.horario,
    t.descricao,
    f.nome AS nome_professor
FROM historico_treino AS ht
JOIN treino AS t ON t.idtreino = ht.treino_id
JOIN perfil_usuario AS f ON f.usuario_id = t.funcionario_id
JOIN usuario AS u ON u.idusuario = ht.usuario_id
WHERE t.idtreino = ?
";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "i", $idusuario);
  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_treinos = [];
  while ($treino = mysqli_fetch_assoc($resultados)) {
    $lista_treinos[] = $treino;
  }

  mysqli_stmt_close($comando);
  return $lista_treinos;
}

function listarTreinoTipo($tipo)
{
  $conexao = conectar();
  $sql = "SELECT 
  pf.nome AS nome_usuario,
  t.tipo,
  t.horario,
  t.descricao
  FROM treino as t
  JOIN perfil_usuario AS pf ON pf.usuario_id = t.usuario_id
  WHERE t.tipo = ?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "s", $tipo);
  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_treinos = [];
  while ($treino = mysqli_fetch_assoc($resultados)) {
    $lista_treinos[] = $treino;
  }

  mysqli_stmt_close($comando);
  return $lista_treinos;
}

function cadastrarHistoricoTreino($idusuario, $idtreino, $data_execucao, $observacoes)
{
  $conexao = conectar();
  $sql = " INSERT INTO historico_treino (usuario_id, treino_id, data_execucao, observacoes) VALUES (?, ?, ?, ?)";
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, "iiss", $idusuario, $idtreino, $data_execucao, $observacoes);
  $funcionou = mysqli_stmt_execute($comando);

  $idInserido = null;
  if ($funcionou) {
    $idInserido = mysqli_insert_id($conexao);
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $idInserido; // retorna o ID inserido, ou null se não funcionou
}

function listarAulaAgendada($idaula = null)
{
  $conexao = conectar();

  $sql = "SELECT      
      ag.idaula,
      ag.data_aula,
      ag.dia_semana,
      ag.hora_inicio,
      ag.hora_fim,
      t.idtreino,
      t.tipo AS tipo_treino,
      t.descricao AS descricao_treino,
      t.horario AS horario_treino,
      te.idtreino2,
      te.series,
      te.repeticoes,
      te.carga,
      te.intervalo_segundos,
      ex.idexercicio,
      ex.nome AS nome_exercicio,
      ex.grupo_muscular,
      ex.video_url,
      ex.descricao AS descricao_exercicio,
      f.idfuncionario,
      f.nome AS nome_usuario,
      u.email AS email_professor,
      c.nome AS nome_cargo,
      pp.foto_perfil,
      pp.avaliacao_media,
      pp.modalidade,
      pp.descricao AS descricao_professor

    FROM aula_agendada AS ag
    LEFT JOIN treino AS t 
        ON ag.treino_id = t.idtreino
    LEFT JOIN treino_exercicio AS te 
        ON t.idtreino = te.treino_id
    LEFT JOIN exercicio AS ex 
        ON te.exercicio_id = ex.idexercicio
    LEFT JOIN funcionario AS f 
        ON ag.funcionario_id = f.idfuncionario
    LEFT JOIN cargo AS c 
        ON c.idcargo = f.cargo_id
    LEFT JOIN perfil_professor AS pp 
        ON f.usuario_id = pp.usuario_id
    LEFT JOIN usuario AS u 
        ON f.usuario_id = u.idusuario
  ";
  if ($idaula !== null) {
    $sql .= " WHERE ag.idaula = ? ";
  }
  $sql .= " ORDER BY ag.data_aula DESC, ex.nome ASC;";
  $comando = mysqli_prepare($conexao, $sql);
  if (!$comando) {
    echo "Erro na preparação da consulta: " . mysqli_error($conexao);
    return [];
  }
  if ($idaula !== null) {
    mysqli_stmt_bind_param($comando, "i", $idaula);
  }
  mysqli_stmt_execute($comando);
  $resultado = mysqli_stmt_get_result($comando);

  $lista = [];
  while ($linha = mysqli_fetch_assoc($resultado)) {
    $lista[] = $linha;
  }
  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $lista;
}



function listarAulaAgendadaUsuario($idusuario)
{
  $conexao = conectar();

  $sql = "SELECT
                ag.idaula,
                ag.data_aula,
                ag.dia_semana,
                ag.hora_inicio,
                ag.hora_fim,
                ag.treino_id,
                t.idtreino,
                t.tipo AS tipo_treino,
                t.descricao,
                f.idfuncionario,
                f.nome AS nome_usuario
            FROM aula_usuario AS au
            JOIN aula_agendada AS ag ON au.idaula = ag.idaula
            LEFT JOIN treino AS t ON ag.treino_id = t.idtreino
            LEFT JOIN funcionario AS f ON ag.funcionario_id = f.idfuncionario
            WHERE au.usuario_id = ?";

  $comando = mysqli_prepare($conexao, $sql);
  if (!$comando) {
    echo "Erro na preparação: " . mysqli_error($conexao);
    return [];
  }

  mysqli_stmt_bind_param($comando, "i", $idusuario);

  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_aula_agendadas = [];
  while ($aula_agendada = mysqli_fetch_assoc($resultados)) {
    $lista_aula_agendadas[] = $aula_agendada;
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $lista_aula_agendadas;
}

function listarPagamentosDetalhados($idpagamento = null)
{
  $conexao = conectar();

  $sql = "SELECT 
                p.idpagamento, 
                p.valor, 
                p.data_pagamento, 
                p.metodo, 
                p.status AS pagamento_status,
                pd.tipo AS pagamento_tipo, 
                pd.bandeira_cartao, 
                pd.ultimos_digitos, 
                pd.codigo_pix, 
                pd.linha_digitavel_boleto,
                ped.idpedido,
                ped.data_pedido,
                ped.status AS pedido_status,
                pf.usuario_id,
                pf.nome AS nome_usuario,
                u.email AS usuario_email,
                pf.telefone AS usuario_telefone,
                prod.idproduto,
                prod.nome AS produto_nome,
                prod.preco AS produto_preco,
                prod.quantidade_estoque,
                pp.quantidade AS quantidade_produto
            FROM pagamento p
            JOIN pagamento_detalhe pd ON p.idpagamento = pd.pagamento_id
            JOIN pedido ped ON p.idpagamento = ped.pagamento_id
            JOIN usuario u ON ped.usuario_id = u.idusuario
            JOIN perfil_usuario pf ON u.idusuario = pf.usuario_id
            JOIN item_pedido pp ON ped.idpedido = pp.pedido_id
            JOIN produto prod ON pp.produto_id = prod.idproduto";

  if ($idpagamento !== null) {
    $sql .= " WHERE p.idpagamento = ?";
  }

  $sql .= " ORDER BY p.idpagamento, ped.idpedido";

  $comando = mysqli_prepare($conexao, $sql);
  if (!$comando) {
    echo "Erro na preparação: " . mysqli_error($conexao);
    desconectar($conexao);
    return [];
  }

  if ($idpagamento !== null) {
    mysqli_stmt_bind_param($comando, "i", $idpagamento);
  }

  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_pagamento_detalhados = [];
  while ($pagamento_detalhado = mysqli_fetch_assoc($resultados)) {
    $lista_pagamento_detalhados[] = $pagamento_detalhado;
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $lista_pagamento_detalhados;
}

function listarMetaUsuario($idmeta = null)
{
  $conexao = conectar();

  if ($idmeta != null) {
    // Busca apenas a meta específica
    $sql = "SELECT
              idmeta,
              m.usuario_id,
              pf.nome AS nome_usuario,
              m.idmeta,
              m.descricao,
              m.data_inicio,
              m.data_limite,
              m.status
            FROM meta_usuario AS m
            JOIN perfil_usuario AS pf ON m.usuario_id = pf.usuario_id
            WHERE pf.usuario_id = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, "i", $idmeta);
  } else {
    // Busca todas as metas
    $sql = "SELECT 
              idmeta,
              m.usuario_id,
              pf.nome AS nome_usuario,
              m.idmeta,
              m.descricao,
              m.data_inicio,
              m.data_limite,
              m.status
            FROM meta_usuario AS m
            JOIN perfil_usuario AS pf ON m.usuario_id = pf.usuario_id";
    $comando = mysqli_prepare($conexao, $sql);
  }

  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_meta_usuarios = [];
  while ($meta_usuario = mysqli_fetch_assoc($resultados)) {
    $lista_meta_usuarios[] = $meta_usuario;
  }

  mysqli_stmt_close($comando);

  return $lista_meta_usuarios;
}


function listarAvaliacaoFisica($usuarioId)
{
  $conexao = conectar();

  // Se não passou usuário, já retorna falso
  if ($usuarioId == null) {
    $sql = "SELECT
                a.idavaliacao,
                pf.nome AS nome_usuario,
                a.peso,
                a.altura,
                a.imc,
                a.percentual_gordura,
                a.data_avaliacao
            FROM avaliacao_fisica AS a
            JOIN perfil_usuario AS pf ON a.usuario_id = pf.usuario_id";
    $comando = mysqli_prepare($conexao, $sql);
  } else {
    $sql = "SELECT
                a.idavaliacao,
                pf.nome AS nome_usuario,
                a.peso,
                a.altura,
                a.imc,
                a.percentual_gordura,
                a.data_avaliacao
            FROM avaliacao_fisica AS a
            JOIN perfil_usuario AS pf ON a.usuario_id = pf.usuario_id
            WHERE a.usuario_id = ?
            ORDER BY a.data_avaliacao DESC";  // só pega a avaliação mais recente
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, "i", $usuarioId);
  }
  if (!$comando) {
    // Erro na preparação, pode tratar ou retornar false
    return false;
  }
  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);
  $lista_avaliacoes = [];
  while ($avaliacao = mysqli_fetch_assoc($resultados)) {
    $lista_avaliacoes[] = $avaliacao;
  }

  mysqli_stmt_close($comando);
  mysqli_close($conexao);

  // Retorna o array da avaliação ou false caso não tenha
  return $lista_avaliacoes;
}


function listarCargo($idcargo)
{
  $conexao = conectar();

  if ($idcargo != null) {
    $sql = " SELECT *
    FROM cargo 
    WHERE idcargo = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, "i", $idcargo);
  } else {
    $sql = " SELECT * FROM cargo";
    $comando = mysqli_prepare($conexao, $sql);
  }

  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_cargos = [];
  while ($cargo = mysqli_fetch_assoc($resultados)) {
    $lista_cargos[] = $cargo;
  }

  mysqli_stmt_close($comando);

  return $lista_cargos;
}

function listarRefeicoes($idrefeicao)
{
  $conexao = conectar();

  if ($idrefeicao !== null) {
    $sql = " SELECT 
    idrefeicao,
    usuario_id,
    pu.nome AS nome_usuario,
    dieta_id,
    d.descricao,
    d.data_inicio,
    d.data_fim,
    r.tipo,
    r.horario
    FROM refeicao AS r
    JOIN dieta AS d ON r.dieta_id = d.iddieta
    JOIN perfil_usuario AS pu ON d.usuario_id = pu.usuario_id
    WHERE idrefeicao = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, "i", $idrefeicao);
  } else {
    $sql = " SELECT 
    idrefeicao,
    dieta_id,
    d.descricao,
    d.data_inicio,
    d.data_fim,
    r.tipo,
    r.horario
    FROM refeicao AS r
    JOIN dieta AS d ON r.dieta_id = d.iddieta";
    $comando = mysqli_prepare($conexao, $sql);
  }

  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_refeicaos = [];
  while ($refeicao = mysqli_fetch_assoc($resultados)) {
    $lista_refeicaos[] = $refeicao;
  }

  mysqli_stmt_close($comando);

  return $lista_refeicaos;
}

function listarAlimentos($idalimento = null)
{
  $conexao = conectar();

  if ($idalimento !== null) {
    $sql = "SELECT * FROM alimento WHERE idalimento = ?";
    $comando = mysqli_prepare($conexao, $sql);
    if (!$comando) {
      echo "Erro na preparação: " . mysqli_error($conexao);
      return [];
    }
    mysqli_stmt_bind_param($comando, "i", $idalimento);
  } else {
    $sql = "SELECT * FROM alimento";
    $comando = mysqli_prepare($conexao, $sql);
    if (!$comando) {
      echo "Erro na preparação: " . mysqli_error($conexao);
      return [];
    }
  }

  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_alimentos = [];
  while ($alimento = mysqli_fetch_assoc($resultados)) {
    $lista_alimentos[] = $alimento;
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $lista_alimentos;
}


function listarCategoriaProduto($idcategoria)
{
  $conexao = conectar();

  if ($idcategoria !== null) {
    $sql = " SELECT * FROM categoria_produto WHERE $idcategoria = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, "i", $idcategoria);
  } else {
    $sql = " SELECT * FROM categoria_produto";
    $comando = mysqli_prepare($conexao, $sql);
  }

  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_categoria_produtos = [];
  while ($categoria_produto = mysqli_fetch_assoc($resultados)) {
    $lista_categoria_produtos[] = $categoria_produto;
  }

  mysqli_stmt_close($comando);

  return $lista_categoria_produtos;
}



function listarRespostaForum($idresposta)
{
  $conexao = conectar();

  // Verifica se $idresposta não é nulo
  if ($idresposta !== null) {
    // Consulta com junção para pegar o nome do usuário ao invés do id
    $sql = " SELECT 
     rf.idresposta, 
     rf.mensagem, 
     rf.data_resposta, 
     pf.nome AS nome_usuario, 
     rf.forum_id, 
     f.descricao
    FROM resposta_forum rf
    JOIN perfil_usuario AS pf ON rf.usuario_id = pf.usuario_id
    JOIN forum AS f ON rf.forum_id = f.idtopico
    WHERE rf.forum_id = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, "i", $idresposta);
  } else {
    // Consulta para pegar todas as respostas com os nomes dos usuários
    $sql = " SELECT 
     rf.idresposta, 
     rf.usuario_id,
     rf.mensagem, 
     rf.data_resposta, 
     pf.nome AS nome_usuario, 
     rf.forum_id, f.descricao
    FROM resposta_forum rf
    JOIN perfil_usuario AS pf ON rf.usuario_id = pf.usuario_id
    JOIN forum AS f ON rf.forum_id = f.idtopico";
    $comando = mysqli_prepare($conexao, $sql);
  }

  // Executa a consulta
  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  // Cria um array para armazenar as respostas do fórum
  $lista_resposta_forums = [];
  while ($resposta_forum = mysqli_fetch_assoc($resultados)) {
    $lista_resposta_forums[] = $resposta_forum;
  }

  // Fecha a preparação do comando
  mysqli_stmt_close($comando);

  return $lista_resposta_forums;
}

function listarItemPedido($usuario_id): array
{
  $conexao = conectar();

  if ($usuario_id != null) {
    $sql = "
        SELECT 
            ped.idpedido, 
            pf.nome AS nome_usuario, 
            p.nome AS produto_nome, 
            ip.quantidade, 
            ip.preco_unitario, 
            ped.status, 
            ped.data_pedido
        FROM pedido ped
        JOIN item_pedido ip ON ped.idpedido = ip.pedido_id
        JOIN produto p ON ip.produto_id = p.idproduto
        JOIN perfil_usuario pf ON ped.usuario_id = pf.usuario_id
        WHERE ped.usuario_id = ?
        ORDER BY ped.data_pedido DESC
    ";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, "i", $usuario_id);
  } else {
    $sql = "    SELECT 
            ped.idpedido, 
            pf.nome AS nome_usuario, 
            p.nome AS produto_nome, 
            ip.quantidade, 
            ip.preco_unitario, 
            ped.status, 
            ped.data_pedido
        FROM pedido ped
        JOIN item_pedido ip ON ped.idpedido = ip.pedido_id
        JOIN produto p ON ip.produto_id = p.idproduto
        JOIN perfil_usuario pf ON ped.usuario_id = pf.usuario_id
        ORDER BY ped.data_pedido DESC";
    $comando = mysqli_prepare($conexao, $sql);
  }
  mysqli_stmt_execute($comando);
  $resultado = mysqli_stmt_get_result($comando);

  $pedidos = [];
  while ($pedido = mysqli_fetch_assoc($resultado)) {
    $pedidos[] = $pedido;
  }

  mysqli_stmt_close($comando);
  return $pedidos;
}


function listarItemPedidosComFiltros($usuario_id, $status = null, $data_inicio = null, $data_fim = null, $produto_nome = null, $preco_min = null, $preco_max = null)
{
  $conexao = conectar();

  // Montar a consulta base com os filtros
  $sql = "
    SELECT 
        ped.idpedido, 
        pf.nome AS nome_usuario, 
        p.nome AS produto_nome, 
        ip.quantidade, 
        ip.preco_unitario, 
        ped.status, 
        ped.data_pedido
    FROM pedido ped
    JOIN item_pedido ip ON ped.idpedido = ip.pedido_id
    JOIN produto p ON ip.produto_id = p.idproduto
    JOIN usuario u ON ped.usuario_id = pf.usuario_id
    WHERE ped.usuario_id = ?
    ";

  // Filtros dinâmicos para status, data, produto e preço
  if ($status !== null) {
    $sql .= " AND ped.status = ?";
  }
  if ($data_inicio !== null && $data_fim !== null) {
    $sql .= " AND ped.data_pedido BETWEEN ? AND ?";
  }
  if ($produto_nome !== null) {
    $sql .= " AND p.nome LIKE ?";
  }
  if ($preco_min !== null && $preco_max !== null) {
    $sql .= " AND ip.preco_unitario BETWEEN ? AND ?";
  }

  $sql .= " ORDER BY ped.data_pedido DESC";

  // Preparar a consulta
  $comando = mysqli_prepare($conexao, $sql);

  // Bind dos parâmetros dinamicamente
  $bind_types = "i"; // Inicia com o tipo do ID do usuário (inteiro)
  $params = [$usuario_id];

  // Adicionar parâmetros conforme os filtros fornecidos
  if ($status !== null) {
    $bind_types .= "s"; // Tipo string para o status
    $params[] = $status;
  }
  if ($data_inicio !== null && $data_fim !== null) {
    $bind_types .= "ss"; // Tipo string para as datas
    $params[] = $data_inicio;
    $params[] = $data_fim;
  }
  if ($produto_nome !== null) {
    $bind_types .= "s"; // Tipo string para o nome do produto
    $params[] = "%" . $produto_nome . "%";
  }
  if ($preco_min !== null && $preco_max !== null) {
    $bind_types .= "dd"; // Tipo decimal para os preços
    $params[] = $preco_min;
    $params[] = $preco_max;
  }

  // Bind dos parâmetros
  mysqli_stmt_bind_param($comando, $bind_types, ...$params);

  // Executar a consulta
  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $pedidos = [];
  while ($pedido = mysqli_fetch_assoc($resultados)) {
    $pedidos[] = $pedido;
  }

  mysqli_stmt_close($comando);
  return $pedidos;
}

function listarUsuario($idusuario)
{
  $conexao = conectar();

  if ($idusuario != null) {
    $sql = " SELECT * FROM usuario WHERE idusuario = ? ";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, "i", $idusuario);
  } else {
    $sql = " SELECT * FROM usuario";
    $comando = mysqli_prepare($conexao, $sql);
  }

  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_usuarios = [];
  while ($usuario = mysqli_fetch_assoc($resultados)) {
    $lista_usuarios[] = $usuario;
  }

  mysqli_stmt_close($comando);

  return $lista_usuarios;
}

function listarUsuarioTipo($tipo)
{
  $conexao = conectar();


  $sql = " SELECT * FROM usuario WHERE tipo_usuario = ?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "i", $tipo);

  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_usuarios = [];
  while ($usuario = mysqli_fetch_assoc($resultados)) {
    $lista_usuarios[] = $usuario;
  }

  mysqli_stmt_close($comando);

  return $lista_usuarios;
}

function listarAssinaturas($idassinatura)
{
  $conexao = conectar();

  if ($idassinatura !== null) {
    $sql = "SELECT 
    idassinatura,
      pf.nome AS nome_usuario,
      p.tipo,
      p.duracao,
      a.data_inicio,
      a.data_fim
    FROM assinatura AS a
    JOIN plano AS p ON a.plano_id = p.idplano
    JOIN perfil_usuario AS pf ON a.usuario_id = pf.usuario_id
    WHERE a.idassinatura = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, "i", $idassinatura);
  } else {
    $sql = "SELECT 
    idassinatura,
      pf.nome AS nome_usuario,
      p.tipo,
      p.duracao,
      a.data_inicio,
      a.data_fim
    FROM assinatura AS a
    JOIN plano AS p ON a.plano_id = p.idplano
    JOIN perfil_usuario AS pf ON a.usuario_id = pf.usuario_id";
    $comando = mysqli_prepare($conexao, $sql);
  }

  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_assinaturas = [];
  while ($assinatura = mysqli_fetch_assoc($resultados)) {
    $lista_assinaturas[] = $assinatura;
  }

  mysqli_stmt_close($comando);

  return $lista_assinaturas;
}


function deletarDietaAlimentar($iddieta, $idalimento)
{
  $conexao = conectar();
  $sql = "DELETE FROM dieta_alimentar WHERE $iddieta = ? and $idalimento = ?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "ii", $iddieta, $alimento);
  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  return $funcionou; // Retorna true se a exclusão foi bem-sucedida, false caso contrário
}

function deletarPlano($idplano)
{
  $conexao = conectar();
  $sql = "DELETE FROM plano WHERE $idplano = ?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "i", $idplano);
  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  return $funcionou;
}

function deletarAlimento($idalimento)
{
  $conexao = conectar();
  $sql = "DELETE FROM alimento WHERE $idalimento = ?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "i", $idalimento);
  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  return $funcionou; // Retorna true se a exclusão foi bem-sucedida, false caso contrário
}

function deletarCategoriaProduto($idcategoria)
{
  $conexao = conectar();
  $sql = "DELETE FROM categoria_produto WHERE $idcategoria = ?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "i", $idcategoria);
  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  return $funcionou; // Retorna true se a exclusão foi bem-sucedida, false caso contrário
}

function deletarPagamentoDetalhe($idpagaemento2)
{
  $conexao = conectar();
  $sql = "DELETE FROM pagamento_detalhe WHERE idpagamento2 = ?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "i", $idpagamento2);
  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  return $funcionou; // Retorna true se a exclusão foi bem-sucedida, false caso contrário
}

function deletarItemPedido($iditem)
{
  $conexao = conectar();
  $sql = "DELETE FROM item_pedido WHERE pedido_id = ?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "i", $iditem);
  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  return $funcionou; // Retorna true se a exclusão foi bem-sucedida, false caso contrário
}

function deletarAulaAgendada($idaula)
{
  $conexao = conectar();
  $sql = "DELETE FROM aula_agendada WHERE $idaula = ?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "i", $idaula);
  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  return $funcionou; // Retorna true se a exclusão foi bem-sucedida, false caso contrário
}

function deletarTreino($idtreino)
{
  $conexao = conectar();
  $sql = "DELETE FROM treino WHERE $idtreino = ?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "i", $idtreino);
  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  return $funcionou; // Retorna true se a exclusão foi bem-sucedida, false caso contrário
}

function deletarHistoricoTreino($idhistorico)
{
  $conexao = conectar();
  $sql = "DELETE FROM historico_treino WHERE $idhistorico = ?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "i", $idhistorico);
  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  return $funcionou; // Retorna true se a exclusão foi bem-sucedida, false caso contrário
}

function deletarPedido($idpedido)
{
  $conexao = conectar();
  $sql = "DELETE FROM pedido WHERE $idpedido = ?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "i", $idpedido);
  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  return $funcionou; // Retorna true se a exclusão foi bem-sucedida, false caso contrário
}

function deletarExercicio($idexercicio): bool
{
  $conexao = conectar();

  // Corrigindo o SQL: o campo da tabela deve estar à esquerda
  $sql = "DELETE FROM exercicio WHERE idexercicio = ?";
  $comando = mysqli_prepare($conexao, $sql);

  if (!$comando) {
    echo "Erro na preparação: " . mysqli_error($conexao);
    return false;
  }

  mysqli_stmt_bind_param($comando, "i", $idexercicio);

  $funcionou = mysqli_stmt_execute($comando);


  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}


function cadastrarCategoriaProduto($nome, $descricao)
{
  $conexao = conectar();
  $sql = " INSERT INTO categoria_produto (nome, descricao) VALUES(?, ?)";
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_bind_param($comando, "ss", $nome, $descricao);
  $funcionou = mysqli_stmt_execute($comando);

  $idInserido = null;
  if ($funcionou) {
    $idInserido = mysqli_insert_id($conexao);
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $idInserido; // retorna o ID inserido, ou null se não funcionou
}

function editarProduto($idproduto, $nome, $descricao, $preco, $quantidade_estoque, $imagem)
{
  $conexao = conectar();

  $sql = "UPDATE produto 
          SET nome = ?, descricao = ?, preco = ?, quantidade_estoque = ?, imagem = ?
          WHERE idproduto = ?";

  $comando = mysqli_prepare($conexao, $sql);

  if (!$comando) {
    echo "Erro na preparação: " . mysqli_error($conexao);
    return false;
  }

  // Ordem correta dos parâmetros
  mysqli_stmt_bind_param($comando, "ssdisi", $nome, $descricao, $preco, $quantidade_estoque, $imagem, $idproduto);

  $funcionou = mysqli_stmt_execute($comando);

  if (!$funcionou) {
    echo "Erro na execução: " . mysqli_stmt_error($comando);
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function editarRespostaForum($idresposta, $mensagem, $usuario_id, $forum_id)
{
  $conexao = conectar();

  $sql = "UPDATE resposta_forum 
          SET mensagem = ?, usuario_id = ?, forum_id = ?
          WHERE idresposta = ?";

  $comando = mysqli_prepare($conexao, $sql);

  if (!$comando) {
    echo "Erro na preparação: " . mysqli_error($conexao);
    return false;
  }

  // Ordem correta dos parâmetros
  mysqli_stmt_bind_param($comando, "siii", $mensagem, $usuario_id, $forum_id, $idresposta);

  $funcionou = mysqli_stmt_execute($comando);

  if (!$funcionou) {
    echo "Erro na execução: " . mysqli_stmt_error($comando);
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function cadastrarProduto($nome, $descricao, $preco, $quantidade_estoque, $imagem)
{
  $conexao = conectar();

  $sql = " INSERT INTO produto (nome, descricao, preco, quantidade_estoque, imagem) VALUES (?,?,?,?,?)";

  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "ssdis", $nome, $descricao, $preco, $quantidade_estoque, $imagem);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function cadastrarItemPedido($pedido_id, $produto_id, $quantidade, $preco_unitario): bool
{
  $conexao = conectar();

  $sql = " INSERT INTO item_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?,?,?,?)";

  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "iiid", $pedido_id, $produto_id, $quantidade, $preco_unitario);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function cadastrarPagamento($valor, $data_pagamento, $metodo, $status = 'sucesso')
{
  $conexao = conectar();

  $sql = "INSERT INTO pagamento (valor, data_pagamento, metodo, status)
            VALUES (?, ?, ?, ?)";

  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "dsss", $valor, $data_pagamento, $metodo, $status);

  $funcionou = mysqli_stmt_execute($comando);
  $idpagamento = 0;
  if ($funcionou) {
    $idpagamento = mysqli_insert_id($conexao);
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $idpagamento;
}

function editarPedido($idpedido, $usuario_id, $data_pedido, $status)
{
  $conexao = conectar();

  $sql = "UPDATE pedido 
          SET usuario_id = ?, data_pedido = ?, status = ?
          WHERE idpedido = ?";

  $comando = mysqli_prepare($conexao, $sql);

  if (!$comando) {
    echo "Erro na preparação: " . mysqli_error($conexao);
    return false;
  }

  // Ordem correta dos parâmetros
  mysqli_stmt_bind_param($comando, "issi", $usuario_id, $data_pedido, $status, $idpedido);

  $funcionou = mysqli_stmt_execute($comando);

  if (!$funcionou) {
    echo "Erro na execução: " . mysqli_stmt_error($comando);
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function cadastrarAlimento($nome, $calorias, $carboidratos, $proteinas, $gorduras, $porcao, $categoria, $imagem)
{
  $conexao = conectar();

  $sql = " INSERT INTO alimento (nome, calorias, carboidratos, proteinas, gorduras, porcao, categoria, foto_de_perfil)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "sddddsss", $nome, $calorias, $carboidratos, $proteinas, $gorduras, $porcao, $categoria, $imagem);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function cadastrarCargo($nome, $descricao)
{
  $conexao = conectar();

  $sql = " INSERT INTO cargo  (nome, descricao) VALUES (?, ?)";

  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "ss", $nome, $descricao);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function cadastrarExercicio($nome, $grupo_muscular, $descricao, $video_url)
{
  $conexao = conectar();

  $sql = " INSERT INTO exercicio  (nome, grupo_muscular, descricao,  video_url) VALUES (?, ?, ?,?)";

  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "ssss", $nome, $grupo_muscular, $descricao, $video_url);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function editarAssinatura($idassinatura, $data_inicio, $data_fim, $usuario_id)
{
  $conexao = conectar();

  $sql = "UPDATE assinatura 
            SET data_inicio = ?, data_fim = ?, usuario_id = ?
            WHERE idassinatura = ?";

  $comando = mysqli_prepare($conexao, $sql);

  if (!$comando) {
    echo "Erro na preparação: " . mysqli_error($conexao);
    return false;
  }

  // Ordem correta dos parâmetros: data_inicio, data_fim, usuario_id, idassinatura
  mysqli_stmt_bind_param($comando, "ssii", $data_inicio, $data_fim, $usuario_id, $idassinatura);

  $funcionou = mysqli_stmt_execute($comando);

  if (!$funcionou) {
    echo "Erro na execução: " . mysqli_stmt_error($comando);
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function editarPagamentoDetalhe($idpagamento2, $pagamento_id, $tipo, $bandeira_cartao, $ultimos_digitos, $codigo_pix, $linha_digitavel_boleto): bool
{
  $conexao = conectar();

  $sql = "UPDATE pagamento_detalhe 
               SET pagamento_id = ?, 
                   tipo = ?, 
                   bandeira_cartao = ?, 
                   ultimos_digitos = ?, 
                   codigo_pix = ?, 
                   linha_digitavel_boleto = ?
             WHERE idpagamento2 = ?";

  $comando = mysqli_prepare($conexao, $sql);

  if (!$comando) {
    echo "Erro na preparação: " . mysqli_error($conexao);
    return false;
  }

  // Ordem: pagamento_id (i), tipo (s), bandeira_cartao (s), ultimos_digitos (s), codigo_pix (s), linha_digitavel_boleto (s), idpagamento2 (i)
  mysqli_stmt_bind_param(
    $comando,
    "isssssi",
    $pagamento_id,
    $tipo,
    $bandeira_cartao,
    $ultimos_digitos,
    $codigo_pix,
    $linha_digitavel_boleto,
    $idpagamento2
  );

  $funcionou = mysqli_stmt_execute($comando);

  if (!$funcionou) {
    echo "Erro na execução: " . mysqli_stmt_error($comando);
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function cadastrarDieta($descricao, $data_inicio, $data_fim, $usuario_id)
{
  $conexao = conectar();

  $sql = "INSERT INTO dieta (descricao, data_inicio, data_fim, usuario_id)
            VALUES (?, ?, ?, ?)";

  $comando = mysqli_prepare($conexao, $sql);

  if (!$comando) {
    echo "Erro na preparação: " . mysqli_error($conexao);
    return false;
  }

  // Ordem dos parâmetros: descricao (s), data_inicio (s), data_fim (s), usuario_id (i)
  mysqli_stmt_bind_param($comando, "sssi", $descricao, $data_inicio, $data_fim, $usuario_id);

  $funcionou = mysqli_stmt_execute($comando);

  if (!$funcionou) {
    echo "Erro na execução: " . mysqli_stmt_error($comando);
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function cadastrarDietaAlimentar($idrefeicao, $idalimento, $quantidade, $observacao): bool
{
  $conexao = conectar();

  $sql = "INSERT INTO dieta_alimentar (refeicao_id, alimento_id, quantidade, observacao)
            VALUES (?, ?, ?, ?)";

  $comando = mysqli_prepare($conexao, $sql);

  if (!$comando) {
    echo "Erro na preparação: " . mysqli_error($conexao);
    return false;
  }

  // Ordem dos parâmetros: refeicao_id (i), alimento_id (i), quantidade (d), observacao (s)
  mysqli_stmt_bind_param($comando, "iids", $idrefeicao, $idalimento, $quantidade, $observacao);

  $funcionou = mysqli_stmt_execute($comando);

  if (!$funcionou) {
    echo "Erro na execução: " . mysqli_stmt_error($comando);
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}
//

function cadastrarFuncionario($nome, $data_contratacao, $salario, $cargo_id, $usuario_id): bool
{
  $conexao = conectar();

  $sql = "INSERT INTO funcionario (nome,  data_contratacao, salario, cargo_id, usuario_id)
            VALUES (?, ?, ?, ?, ?)";

  $comando = mysqli_prepare($conexao, $sql);

  if (!$comando) {
    echo "Erro na preparação: " . mysqli_error($conexao);
    return false;
  }

  mysqli_stmt_bind_param($comando, "sssii", $nome,  $data_contratacao, $salario, $cargo_id, $usuario_id);

  $funcionou = mysqli_stmt_execute($comando);

  if (!$funcionou) {
    echo "Erro na execução: " . mysqli_stmt_error($comando);
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function editarRefeicao($idrefeicao, $dieta_id, $tipo, $horario)
{
  $conexao = conectar();

  $sql = "UPDATE refeicao 
            SET dieta_id = ?, tipo = ?, horario = ?
            WHERE idrefeicao = ?";

  $comando = mysqli_prepare($conexao, $sql);

  if (!$comando) {
    echo "Erro na preparação: " . mysqli_error($conexao);
    return false;
  }

  mysqli_stmt_bind_param($comando, "issi", $dieta_id, $tipo, $horario, $idrefeicao);

  $funcionou = mysqli_stmt_execute($comando);

  if (!$funcionou) {
    echo "Erro na execução: " . mysqli_stmt_error($comando);
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function editarAlimento($idalimento, $nome, $calorias, $carboidratos, $proteinas, $gorduras, $porcao, $categoria, $imagem)
{
  $conexao = conectar();

  $sql = "UPDATE alimento 
            SET nome = ?, calorias = ?, carboidratos = ?, proteinas = ?, gorduras = ?, porcao = ?, categoria = ?, foto_de_perfil = ?
            WHERE idalimento = ?";

  $comando = mysqli_prepare($conexao, $sql);

  if (!$comando) {
    echo "Erro na preparação: " . mysqli_error($conexao);
    return false;
  }

  mysqli_stmt_bind_param($comando, "sddddsssi", $nome, $calorias, $carboidratos, $proteinas, $gorduras, $porcao, $categoria, $imagem, $idalimento);

  $funcionou = mysqli_stmt_execute($comando);

  if (!$funcionou) {
    echo "Erro na execução: " . mysqli_stmt_error($comando);
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function cadastrarMetaUsuario($usuario_id, $descricao, $data_inicio, $data_limite, $status)
{
  $conexao = conectar();

  $sql = "INSERT INTO meta_usuario (usuario_id, descricao, data_inicio, data_limite, status) VALUES (?, ?, ?, ?, ?)";

  $comando = mysqli_prepare($conexao, $sql);

  if (!$comando) {
    echo "Erro na preparação: " . mysqli_error($conexao);
    return false;
  }

  mysqli_stmt_bind_param($comando, "issss", $usuario_id, $descricao, $data_inicio, $data_limite, $status);

  $funcionou = mysqli_stmt_execute($comando);

  if (!$funcionou) {
    echo "Erro na execução: " . mysqli_stmt_error($comando);
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function cadastrarAulaAgendada($data_aula, $dia_semana, $hora_inicio, $hora_fim, $idtreino, $funcionario_id)
{
  $conexao = conectar();

  $sql = "INSERT INTO aula_agendada (data_aula, dia_semana, hora_inicio, hora_fim, treino_id, funcionario_id)
            VALUES (?, ?, ?, ?, ?, ?)";

  $comando = mysqli_prepare($conexao, $sql);

  if (!$comando) {
    echo "Erro na preparação: " . mysqli_error($conexao);
    return false;
  }
  mysqli_stmt_bind_param($comando, 'ssssii', $data_aula, $dia_semana, $hora_inicio, $hora_fim,  $idtreino, $funcionario_id);
  $funcionou = mysqli_stmt_execute($comando);

  if (!$funcionou) {
    echo "Erro na execução: " . mysqli_stmt_error($comando);
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $funcionou;
}

function gerarCodigoPix($tamanho = 32)
{
  $caracteres = array_merge(range('a', 'z'), range('0', '9'));
  $codigo = '';

  for ($i = 0; $i < $tamanho; $i++) {
    $codigo .= $caracteres[array_rand($caracteres)];
  }

  return $codigo;
}
// numeração do adms[0], aluno[1] e funcionário(Professores)[2]
function gerarNumeroMatriculaPorTipo($tipo)
{
  // Define o comprimento desejado por tipo
  switch ($tipo) {
    case 0: // ADM
      $comprimento = 5;
      break;
    case 1: // Aluno
      $comprimento = 15;
      break;
    case 2: // Funcionário
      $comprimento = 10;
      break;
    default:
      return "Tipo inválido";
  }

  // Gera um número aleatório com o comprimento certo
  $min = (int)str_pad('1', $comprimento, '0');     // Ex: 10000
  $max = (int)str_pad('', $comprimento, '9');      // Ex: 99999

  return strval(rand($min, $max));
}

function verificarTipoUsuario($email)
{
  $conexao = conectar(); // usa sua função de conexão
  $sql = "SELECT tipo_usuario FROM usuario WHERE email = ?";
  $comando = mysqli_prepare($conexao, $sql);

  if (!$comando) {
    die("Erro ao preparar: " . mysqli_error($conexao));
  }

  mysqli_stmt_bind_param($comando, 's', $email);
  mysqli_stmt_execute($comando);
  mysqli_stmt_bind_result($comando, $tipo);

  if (mysqli_stmt_fetch($comando)) {
    return $tipo;
  } else {
    return false; // email não encontrado
  }
}
/**
 * Recupera informações completas do usuário do banco de dados
 *
 * Esta função conecta ao banco de dados e busca informações detalhadas do usuário
 * incluindo dados pessoais, endereço, assinatura, plano, avaliações físicas,
 * dietas, treinos, metas, histórico, pedidos e pagamentos.
 *
 * @param int $id ID do usuário para buscar as informações
 * @return array Retorna um array associativo com todos os dados do usuário
 * @throws Exception Se houver erro na conexão ou consulta ao banco
 */
function listarUsuarioCompleto($id)
{
  try {
    // Estabelece conexão com o banco
    $conexao = conectar();

    // Verifica se a conexão foi estabelecida
    if ($conexao->connect_error) {
      throw new Exception('Erro ao conectar ao banco: ' . $conexao->connect_error);
    }

    // Sua query enorme - aqui só exibe 1 campo pra exemplo, substitui pela sua completa depois
    $sql = "SELECT
      u.idusuario,
      u.email,
      u.senha,
      u.tipo_usuario,

      pf.nome AS nome_usuario,
      pf.cpf,
      pf.data_nascimento,
      pf.numero_matricula,
      pf.foto_perfil,

      e.cep,
      e.rua,
      e.numero AS numero_endereco,
      e.complemento,
      e.bairro,
      e.cidade,
      e.estado,

      a.idassinatura,
      a.data_inicio AS assinatura_inicio,
      a.data_fim AS assinatura_fim,

      p.idplano,
      p.tipo AS tipo_plano,
      p.duracao AS duracao_plano,

      af.idavaliacao,
      af.peso,
      af.altura,
      af.imc,
      af.percentual_gordura,
      af.data_avaliacao AS avaliacao_data,

      d.iddieta,
      d.descricao AS descricao_dieta,
      d.data_inicio AS dieta_inicio,
      d.data_fim AS dieta_fim,

      r.idrefeicao,
      r.tipo AS tipo_refeicao,
      r.horario AS horario_refeicao,

      al.idalimento,
      al.nome AS nome_alimento,
      al.calorias,
      al.carboidratos,
      al.proteinas,
      al.gorduras,

      da.quantidade AS quantidade_alimento,
      da.observacao AS observacao_alimento,

      t.idtreino,
      t.tipo AS tipo_treino,
      t.horario AS horario_treino,
      t.descricao AS descricao_treino,

      te.idtreino2,
      te.series,
      te.repeticoes,
      te.carga,
      te.intervalo_segundos,

      ex.idexercicio,
      ex.nome AS nome_exercicio,
      ex.grupo_muscular,

      ht.idhistorico,
      ht.data_execucao AS historico_data,
      ht.observacoes AS historico_observacoes,

      mu.idmeta,
      mu.descricao AS descricao_meta,
      mu.data_inicio AS meta_inicio,
      mu.data_limite AS meta_limite,
      mu.status AS meta_status,

      f.idtopico,
      f.titulo AS titulo_topico,
      f.descricao AS descricao_topico,
      f.data_criacao AS topico_data,

      rf.idresposta,
      rf.mensagem,
      rf.data_resposta AS resposta_data,

      pd.idpedido,
      pd.data_pedido AS pedido_data,
      pd.status AS status_pedido,

      ip.quantidade AS quantidade_produto,
      ip.preco_unitario AS preco_unitario_produto,

      pr.idproduto,
      pr.nome AS nome_produto,
      pr.descricao AS descricao_produto,
      pr.preco AS preco_produto,

      pg.idpagamento,
      pg.valor AS pagamento_valor,
      pg.data_pagamento AS pagamento_data,
      pg.metodo AS pagamento_metodo,
      pg.status AS status_pagamento,

      pd2.tipo AS tipo_pagamento,
      pd2.bandeira_cartao,
      pd2.ultimos_digitos,

      rs.idrecuperacao_senha,
      rs.codigo AS codigo_recuperacao,
      rs.tempo_expiracao

  FROM usuario AS u
  LEFT JOIN perfil_usuario AS pf ON u.idusuario = pf.usuario_id
  LEFT JOIN endereco AS e ON u.idusuario = e.usuario_id
  LEFT JOIN assinatura AS a ON u.idusuario = a.usuario_id
  LEFT JOIN plano AS p ON a.plano_id = p.idplano
  LEFT JOIN avaliacao_fisica AS af ON u.idusuario = af.usuario_id
  LEFT JOIN dieta AS d ON u.idusuario = d.usuario_id
  LEFT JOIN refeicao AS r ON d.iddieta = r.dieta_id
  LEFT JOIN dieta_alimentar AS da ON r.idrefeicao = da.refeicao_id
  LEFT JOIN alimento AS al ON da.alimento_id = al.idalimento
  LEFT JOIN treino AS t ON u.idusuario = t.funcionario_id
  LEFT JOIN treino_exercicio AS te ON t.idtreino = te.treino_id
  LEFT JOIN exercicio AS ex ON te.exercicio_id = ex.idexercicio
  LEFT JOIN historico_treino AS ht ON u.idusuario = ht.usuario_id
  LEFT JOIN meta_usuario AS mu ON u.idusuario = mu.usuario_id
  LEFT JOIN forum AS f ON u.idusuario = f.usuario_id
  LEFT JOIN resposta_forum AS rf ON f.idtopico = rf.forum_id
  LEFT JOIN pedido AS pd ON u.idusuario = pd.usuario_id
  LEFT JOIN item_pedido AS ip ON pd.idpedido = ip.pedido_id
  LEFT JOIN produto AS pr ON ip.produto_id = pr.idproduto
  LEFT JOIN pagamento AS pg ON pd.pagamento_id = pg.idpagamento
  LEFT JOIN pagamento_detalhe AS pd2 ON pg.idpagamento = pd2.pagamento_id
  LEFT JOIN recuperacao_senha AS rs ON u.idusuario = rs.usuario_id

  WHERE u.idusuario = ?
  ;
  ";

    // Executa a query
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $id);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    if (!$resultado) {
      die(json_encode(['error' => 'Erro na query: ' . $conexao->error]));
    }

    // Array para armazenar os resultados
    $dados = [];

    // Processa os resultados
    while ($linha = mysqli_fetch_assoc($resultado)) {
      $dados[] = $linha;
    }

    // Libera o resultado e fecha a conexão
    mysqli_free_result($resultado);
    mysqli_stmt_close($comando);
    desconectar($conexao);

    return $dados;
  } catch (Exception $e) {
    // Log do erro e retorna mensagem amigável
    error_log('Erro em listarUsuarioCompleto: ' . $e->getMessage());
    throw new Exception('Erro ao buscar dados do usuário');
  }
}

function atualizarFotoUsuario($imagem, $idusuario)
{
  $conexao = conectar();
  $sql = 'UPDATE perfil_usuario SET foto_perfil = ? WHERE usuario_id = ?';
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, 'si', $imagem, $idusuario);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function calcularDataFinal($tipoPlano, $dataInicio = null): string
{
  if (!$dataInicio) {
    $dataInicio = date('Y-m-d'); // se não for passado, pega a data atual
  }

  $data = new DateTime($dataInicio);

  switch (strtolower($tipoPlano)) {
    case 'mensal':
      $data->modify('+1 month');
      break;
    case 'trimestral':
      $data->modify('+3 months');
      break;
    case 'semestral':
      $data->modify('+6 months');
      break;
    case 'anual':
      $data->modify('+1 year');
      break;
    default:
      throw new Exception("Tipo de plano inválido.");
  }

  return $data->format('Y-m-d');
}



function enviarResposta($sucesso, $mensagem, $dados = []): never
{
  echo json_encode([
    'sucesso' => $sucesso,
    'mensagem' => $mensagem,
    'dados' => $dados

  ], JSON_UNESCAPED_UNICODE);
  exit;
}

function calcularIMC($pesoKg, $alturaCm): string
{
  $alturaMetros = $alturaCm / 100;

  // Validação básica
  if ($pesoKg <= 0 || $alturaMetros <= 0) {
    return "Peso e altura devem ser maiores que zero.";
  }

  // Cálculo do IMC
  $imc = $pesoKg / ($alturaMetros * $alturaMetros);

  // Retorna o IMC com duas casas decimais
  return number_format($imc, 2);
}


function cadastrarHistoricoPeso($idusuario, $peso, $data_registro): bool
{
  $conexao = conectar();
  $sql = "INSERT INTO historico_peso (peso, data_registro, usuario_id) VALUES (?, ?, ?)";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "dsi", $peso, $data_registro, $idusuario);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function listarHistoricoPeso($idusuario)
{
  $conexao = conectar();

  if ($idusuario != null) {
    $sql = "SELECT * FROM historico_peso WHERE usuario_id = ? ORDER BY data_registro DESC";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, "i", $idusuario);
  } else {
    $sql = "SELECT * FROM historico_peso ORDER BY data_registro DESC";
    $comando = mysqli_prepare($conexao, $sql);
  }

  mysqli_stmt_execute($comando);
  $resultado = mysqli_stmt_get_result($comando);
  $dados = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $dados;
}

function editarHistoricoPeso($idhistorico_peso, $peso)
{
  $conexao = conectar();
  $sql = "UPDATE historico_peso SET peso=? WHERE idhistorico_peso=?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "di", $peso, $idhistorico_peso);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function deletarHistoricoPeso($idhistorico_peso)
{
  $conexao = conectar();
  $sql = "DELETE FROM historico_peso WHERE idhistorico_peso=?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "i", $idhistorico_peso);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function deletarCupomDesconto($idcupom)
{
  $conexao = conectar();
  $sql = "DELETE FROM cupom_desconto WHERE idcupom=?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "i", $idcupom);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function cadastrarPerfilUsuario($idusuario, $nome, $cpf, $data_nasc, $telefone, $numero_matricula, $imagem)
{
  $conexao = conectar();
  $sql = "INSERT INTO perfil_usuario (usuario_id, nome, cpf, data_nascimento, telefone,numero_matricula, foto_perfil) VALUES (?, ?, ?, ?, ?, ?, ?)";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "issssss", $idusuario, $nome, $cpf, $data_nasc, $telefone, $numero_matricula, $imagem);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return json_encode($funcionou, JSON_UNESCAPED_UNICODE);
}

function editarPerfilUsuario($idusuario, $nome, $data_nasc, $telefone, $imagem)
{
  $conexao = conectar();
  $sql = "UPDATE perfil_usuario SET nome=?, data_nascimento=?, telefone=?, foto_perfil=? WHERE usuario_id=?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "ssssi", $nome, $data_nasc, $telefone, $imagem, $idusuario);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function listarPerfilUsuario($idusuario)
{
  $conexao = conectar();
  if ($idusuario != null) {
    $sql = "SELECT
    idperfil_usuario,
    usuario_id,
    nome AS nome_usuario,
    cpf,
    data_nascimento,
    telefone,
    numero_matricula,
    foto_perfil
     FROM perfil_usuario AS pu
     JOIN usuario  AS u ON pu.usuario_id = u.idusuario
    WHERE usuario_id = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, "i", $idusuario);
  } else {
    $sql = "SELECT
    idperfil_usuario,
    usuario_id,
    nome AS nome_usuario,
    cpf,
    data_nascimento,
    telefone,
    numero_matricula,
    foto_perfil
FROM perfil_usuario AS pu
INNER JOIN usuario AS u ON pu.usuario_id = u.idusuario;";
    $comando = mysqli_prepare($conexao, $sql);
  }
  mysqli_stmt_execute($comando);
  $resultado = mysqli_stmt_get_result($comando);
  $dados = [];

  while ($assinatura = mysqli_fetch_assoc($resultado)) {
    $dados[] = $assinatura;
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $dados;
}

function deletarPerfilUsuario($idusuario)
{
  $conexao = conectar();
  $sql = "DELETE FROM perfil_usuario WHERE usuario_id=?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "i", $idusuario);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}



function listarHistoricoPesoUltimo($idusuario)
{
  $conexao = conectar();

  if ($idusuario != null) {
    $sql = "SELECT peso FROM historico_peso WHERE usuario_id = ? ORDER BY data_registro limit 1";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, "i", $idusuario);
  } else {
    $sql = "SELECT * FROM historico_peso ORDER BY data_registro";
    $comando = mysqli_prepare($conexao, $sql);
  }

  mysqli_stmt_execute($comando);
  $resultado = mysqli_stmt_get_result($comando);
  $dados = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $dados;
}

function cadastrarPerfilProfessor($foto_perfil, $modalidade, $avaliacao_media, $descricao, $horarios_disponiveis, $telefone, $usuario_id)
{
  $conexao = conectar();
  $sql = "INSERT INTO perfil_professor 
            (foto_perfil, modalidade, avaliacao_media, descricao, horarios_disponiveis, telefone, usuario_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "ssissis", $foto_perfil, $modalidade, $avaliacao_media, $descricao, $horarios_disponiveis, $telefone, $usuario_id);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

/**
 * Lista informações de perfil de professor(es) com suas aulas agendadas
 * 
 * Esta função consulta o banco de dados para recuperar informações completas
 * do perfil do professor, incluindo dados pessoais, cargo e aulas agendadas.
 * Pode retornar dados de um professor específico ou de todos os professores.
 *
 * @param int|null $idusuario ID do usuário professor para filtrar (opcional)
 * 
 * @return array Retorna um array associativo com os seguintes campos:
 *               - idaula: ID da aula agendada
 *               - data_aula: Data da aula
 *               - dia_semana: Dia da semana (Segunda a Domingo)
 *               - hora_inicio: Hora de início da aula
 *               - hora_fim: Hora de término da aula
 *               - treino_id: ID do treino associado
 *               - nome_professor: Nome completo do professor
 *               - modalidade: Modalidade de ensino do professor
 *               - telefone_professor: Telefone de contato do professor
 *               - email_professor: Email do professor
 *               - cargo_professor: Cargo/função do professor
 * 
 * @throws mysqli_sql_exception Em caso de erro na execução da consulta SQL
 * @example 
 *   // Listar todos os professores com aulas
 *   $todosProfessores = listarPerfilProfessor(null);
 *   
 *   // Listar apenas o professor com ID 5
 *   $professorEspecifico = listarPerfilProfessor(5);
 */
function listarPerfilProfessor($idusuario)
{
  $conexao = conectar();

  if ($idusuario != null) {
    $idusuario += 20;
    $sql = "SELECT
    f.idfuncionario,
    f.usuario_id,
    aa.idaula,
    aa.data_aula,
    aa.dia_semana,
    aa.hora_inicio,
    aa.hora_fim,
    aa.treino_id,
    f.nome AS nome_professor,
    pf.foto_perfil,
    pf.modalidade,
    pf.telefone AS telefone_professor,
    u.email AS email_professor,
    c.nome AS cargo_professor,
    f.salario,
    pf.avaliacao_media,
    pf.descricao,
    pf.horarios_disponiveis,
    pf.data_atualizacao,
    f.data_contratacao
    FROM usuario AS u
    INNER JOIN funcionario AS f ON f.usuario_id = u.idusuario
    INNER JOIN perfil_professor AS pf ON pf.usuario_id = u.idusuario
    INNER JOIN cargo AS c ON c.idcargo = f.cargo_id
    LEFT JOIN aula_agendada AS aa ON aa.funcionario_id = f.idfuncionario
    WHERE u.idusuario = ?
    ";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, "i", $idusuario);
  } else {
    $sql = "SELECT
    f.idfuncionario,
    f.usuario_id,
    aa.idaula,
    aa.data_aula,
    aa.dia_semana,
    aa.hora_inicio,
    aa.hora_fim,
    aa.treino_id,
    f.nome AS nome_professor,
    pf.foto_perfil,
    pf.modalidade,
    pf.telefone AS telefone_professor,
    u.email AS email_professor,
    c.nome AS cargo_professor,
    f.salario,
    pf.avaliacao_media,
    pf.descricao,
    pf.horarios_disponiveis,
    pf.data_atualizacao,
    f.data_contratacao
    FROM usuario AS u
    INNER JOIN funcionario AS f ON f.usuario_id = u.idusuario
    INNER JOIN perfil_professor AS pf ON pf.usuario_id = u.idusuario
    INNER JOIN cargo AS c ON c.idcargo = f.cargo_id
    LEFT JOIN aula_agendada AS aa ON aa.funcionario_id = f.idfuncionario";
    $comando = mysqli_prepare($conexao, $sql);
  }

  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $perfis = [];
  while ($perfil = mysqli_fetch_assoc($resultados)) {
    $perfis[] = $perfil;
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $perfis;
}

function editarPerfilProfessor($idperfil, $usuario_id, $foto_perfil, $modalidade, $avaliacao_media, $descricao, $horarios_disponiveis, $telefone): bool
{
  $conexao = conectar();
  $sql = "UPDATE perfil_professor SET
            usuario_id = ?,
            foto_perfil = ?, 
            modalidade = ?, 
            avaliacao_media = ?, 
            descricao = ?, 
            horarios_disponiveis = ?, 
            telefone = ?
            WHERE idperfil = ?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "ssissssi", $usuario_id, $foto_perfil, $modalidade, $avaliacao_media, $descricao, $horarios_disponiveis, $telefone, $idperfil);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function deletarPerfilProfessor($idperfil)
{
  $conexao = conectar();
  $sql = "DELETE FROM perfil_professor WHERE idperfil = ?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "i", $idperfil);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}


function cadastrarDicaNutricional($titulos, $descricao, $icone, $cor)
{
  $conexao = conectar();
  $sql = "INSERT INTO dicas_nutricionais (titulos, descricao, icone, cor) 
            VALUES (?, ?, ?, ?)";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "ssss", $titulos, $descricao, $icone, $cor);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function listarDicasNutricionais($id = null)
{
  $conexao = conectar();

  if ($id) {
    $sql = "SELECT * FROM dicas_nutricionais WHERE iddicas_nutricionais = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, "i", $id);
  } else {
    $sql = "SELECT * 
FROM dicas_nutricionais
ORDER BY RAND()
LIMIT 1;
";
    $comando = mysqli_prepare($conexao, $sql);
  }

  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $dicas = [];
  while ($dica = mysqli_fetch_assoc($resultados)) {
    $dicas[] = $dica;
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $dicas;
}

function editarDicaNutricional($id, $titulos, $descricao, $icone, $cor)
{
  $conexao = conectar();
  $sql = "UPDATE dicas_nutricionais SET
            titulos = ?, 
            descricao = ?, 
            icone = ?, 
            cor = ?
            WHERE iddicas_nutricionais = ?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "ssssi", $titulos, $descricao, $icone, $cor, $id);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function deletarDicaNutricional($id)
{
  $conexao = conectar();
  $sql = "DELETE FROM dicas_nutricionais WHERE iddicas_nutricionais = ?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "i", $id);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}


function cadastrarAulaUsuario($idaula, $idusuario)
{

  $conexao = conectar();
  $sql = "INSERT INTO aula_usuario (idaula, usuario_id) VALUES (?,?)";

  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "ii", $idaula, $idusuario);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function editarAulaUsuario($idaula, $idusuario)
{

  $conexao = conectar();
  $sql = "UPDATE aula_usuario SET idaula=?, usuario_id=?";

  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "ii", $idaula, $idusuario);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function deletarAulaUsuario($id)
{
  $conexao = conectar();

  $sql = "DELETE FROM aula_usuario WHERE usuario_id = ?";

  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "i", $id);

  $funcionou = mysqli_stmt_execute($comando);
  mysqli_stmt_close($comando);
  desconectar($conexao);
  return $funcionou;
}

function listarAulaUsuario($usuario_id = null)
{
  $conexao = conectar();

  if ($usuario_id !== null) {
    $sql = "SELECT 
              au.idaula,
              au.usuario_id,
              ag.data_aula,
              ag.dia_semana,
              ag.hora_inicio,
              ag.hora_fim,
              ag.treino_id,
              ag.funcionario_id,
              pf.nome AS nome_aluno,
              f.nome AS nome_professor
            FROM aula_usuario AS au
            INNER JOIN aula_agendada AS ag ON au.idaula = ag.idaula
            INNER JOIN perfil_usuario AS pf ON pf.usuario_id = au.usuario_id
            INNER JOIN funcionario AS f ON f.idfuncionario = ag.funcionario_id
            WHERE au.usuario_id = ?;";

    $comando = mysqli_prepare($conexao, $sql);

    if (!$comando) {
      echo "Erro ao preparar SQL: " . mysqli_error($conexao);
      return [];
    }

    mysqli_stmt_bind_param($comando, "i",  $usuario_id);
  } else {
    $sql = "SELECT 
              au.idaula,
              au.usuario_id,
              ag.data_aula,
              ag.dia_semana,
              ag.hora_inicio,
              ag.hora_fim,
              ag.treino_id,
              ag.funcionario_id,
              pf.nome AS nome_aluno,
              f.nome AS nome_professor
            FROM aula_usuario AS au
            INNER JOIN aula_agendada AS ag ON au.idaula = ag.idaula
            INNER JOIN perfil_usuario AS pf ON pf.usuario_id = au.usuario_id
            INNER JOIN funcionario AS f ON f.idfuncionario = ag.funcionario_id;";

    $comando = mysqli_prepare($conexao, $sql);
  }

  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $aulas = [];
  while ($aula = mysqli_fetch_assoc($resultados)) {
    $aulas[] = $aula;
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $aulas;
}

/**
 * Lista as colunas de uma tabela do banco de dados.
 *
 * Abre uma conexão chamando conectar(), escapa o nome da tabela com
 * mysqli_real_escape_string() e executa a consulta "SHOW COLUMNS FROM <tabela>".
 * Retorna um array com as linhas retornadas pela consulta, cada uma representando
 * uma coluna da tabela (normalmente com chaves como 'Field', 'Type', 'Null',
 * 'Key', 'Default' e 'Extra').
 *
 * Observações:
 * - Depende das funções conectar() e desconectar() existentes no projeto.
 * - O nome da tabela é escapado para reduzir risco de injeção, mas ainda é
 *   responsabilidade do chamador fornecer um nome de tabela válido.
 * - Em caso de falha na consulta, a função retorna um array vazio (não lança
 *   exceções por si só). Erros originados por conectar() podem ser propagados.
 * - A função libera o resultado com mysqli_free_result() e encerra a conexão
 *   chamando desconectar().
 *
 * @param string $tabela Nome da tabela cujas colunas serão listadas.
 * @return array<int, array<string, mixed>> Vetor de arrays associativos com os
 *                                        metadados de cada coluna retornados por
 *                                        "SHOW COLUMNS". Pode ser vazio em caso
 *                                        de erro ou se a tabela não possuir colunas.
 */
function listarColunasTabela($tabela)
{
  $conexao = conectar();
  $tabela = mysqli_real_escape_string($conexao, $tabela);
  $sql = "SHOW COLUMNS FROM $tabela";
  $comando = mysqli_query($conexao, $sql);
  $colunas = [];
  while ($coluna = mysqli_fetch_assoc($comando)) {
    $colunas[] = $coluna;
  }

  mysqli_free_result($comando);
  desconectar($conexao);

  return $colunas;
}

function formatarTelefone($numero)
{
  // Remove tudo que não for número
  $tel = preg_replace('/\D/', '', $numero);

  if (strlen($tel) === 11) {
    // Celular com 9 dígitos → (62) 98765-4321
    return "(" . substr($tel, 0, 2) . ") " . substr($tel, 2, 5) . "-" . substr($tel, 7);
  } elseif (strlen($tel) === 10) {
    // Telefone fixo com 8 dígitos → (62) 3456-7890
    return "(" . substr($tel, 0, 2) . ") " . substr($tel, 2, 4) . "-" . substr($tel, 6);
  } else {
    // Caso não seja 10 ou 11 dígitos, retorna como está
    return $numero;
  }
}
/**
 * Retorna a lista de tabelas existentes no banco de dados.
 *
 * Comentários:
 * - Abre uma conexão chamando conectar().
 * - Executa a query "SHOW TABLES" para recuperar as tabelas do schema atual.
 * - Itera sobre o resultado e monta um array com cada linha retornada.
 * - Libera o resultado e fecha a conexão antes de retornar o array.
 *
 * @return array Array de arrays associativos, cada elemento contém a linha retornada pelo SHOW TABLES.
 */
function listarTabelas()
{
  // Abre a conexão com o banco de dados (usa a função conectar() definida no arquivo)
  $conexao = conectar();

  // Query que pede ao MySQL a lista de tabelas do schema atual
  $sql = "SHOW TABLES;";

  // Executa a query. mysqli_query retorna um objeto de resultado ou false em erro.
  $comando = mysqli_query($conexao, $sql);

  // Inicializa o array que receberá as tabelas
  $tabelas = [];

  // Se a execução foi bem-sucedida, percorre o resultado linha a linha
  // mysqli_fetch_assoc devolve cada linha como array associativo até esgotar (retorna null)
  while ($tabela = mysqli_fetch_assoc($comando)) {
    $tabelas[] = $tabela;
  }

  // Libera a memória associada ao resultado
  mysqli_free_result($comando);

  // Fecha a conexão com o banco (usa a função desconectar() definida no arquivo)
  desconectar($conexao);

  // Retorna o array com as tabelas encontradas
  return $tabelas;
}

/**
 * Verifica se um usuário existe pelo email, senha ou tipo de usuário
 * Retorna o usuário encontrado ou false se não existir
 */
function verificarUsuario($email = null, $senha = null, $tipo_usuario = null)
{
  $conexao = conectar();

  // Base da query
  $sql = "SELECT * FROM usuario WHERE 1=1";
  $params = [];
  $tipos = "";

  if ($email !== null) {
    $sql .= " AND email = ?";
    $params[] = $email;
    $tipos .= "s";
  }

  if ($senha !== null) {
    $sql .= " AND senha = ?";
    $params[] = $senha;
    $tipos .= "s";
  }

  if ($tipo_usuario !== null) {
    $sql .= " AND tipo_usuario = ?";
    $params[] = $tipo_usuario;
    $tipos .= "i";
  }

  $comando = mysqli_prepare($conexao, $sql);

  if (!empty($params)) {
    mysqli_stmt_bind_param($comando, $tipos, ...$params);
  }

  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $usuario = mysqli_fetch_assoc($resultados);

  mysqli_stmt_close($comando);

  // Retorna o usuário encontrado ou false se não existir
  return $usuario ?: false;
}

// Função para detectar se a string parece um hash (bcrypt ou argon2)
function is_password_hash_like(string $s): bool
{
  if ($s === '') return false;

  $bcrypt = '/^\$2[ayb]\$(\d{2})\$[\.\/A-Za-z0-9]{53}$/';
  $argon2 = '/^\$argon2(id|i)\$.+/';

  return preg_match($bcrypt, $s) || preg_match($argon2, $s);
}
function listarDietaAlimentar($iddieta)
{
  $conexao = conectar();
  if ($iddieta != null) {
    $sql = 'SELECT
    pf.usuario_id,
    pf.nome AS nome_usuario,
    d.descricao,
    d.data_inicio,
    d.data_fim
    FROM dieta AS d
    JOIN perfil_usuario AS pf ON d.usuario_id = pf.usuario_id
    WHERE d.iddieta=?';
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $iddieta);
  } else {
    $sql = ' SELECT
    pf.usuario_id,
    pf.nome AS nome_usuario,
    d.descricao,
    d.data_inicio,
    d.data_fim
    FROM dieta AS d
    JOIN perfil_usuario AS pf ON d.usuario_id = pf.usuario_id';
    $comando = mysqli_prepare($conexao, $sql);
  }
  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_dietas = [];
  while ($dieta = mysqli_fetch_assoc($resultados)) {
    $lista_dietas[] = $dieta;
  }
  mysqli_stmt_close($comando);

  return $lista_dietas;
}

function DadosGerais($tabela, $id)
{
  $conexao = conectar();
  $id_tabela = pegaIdTabela($tabela);
  $sql = " SELECT * FROM $tabela WHERE $id_tabela = ?";
  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "i", $id);

  mysqli_stmt_execute($comando);
  $resultado = mysqli_stmt_get_result($comando);

  $resposta = mysqli_fetch_assoc($resultado);

  mysqli_stmt_close($comando);


  return $resposta;
}
function DadosGeraisTabela($tabela)
{
  $conexao = conectar();
  $sql = " SELECT * FROM $tabela";
  $comando = mysqli_prepare($conexao, $sql);

  mysqli_stmt_execute($comando);
  $resultado = mysqli_stmt_get_result($comando);

  $resposta = mysqli_fetch_assoc($resultado);

  mysqli_stmt_close($comando);


  return $resposta;
}
function pegaIdTabela($tabela)
{
  switch ($tabela) {
    case 'alimento':
      return 'idalimento';
    case 'plano':
      return 'idplano';
    case 'usuario':
      return 'idusuario';
    case 'assinatura':
      return 'idassinatura';
    case 'cargo':
      return 'idcargo';
    case 'funcionario':
      return 'idfuncionario';
    case 'treino':
      return 'idtreino';
    case 'aula_agendada':
      return 'idaula';
    case 'aula_usuario':
      return 'idaula'; // chave composta, aqui usamos um só
    case 'avaliacao_fisica':
      return 'idavaliacao';
    case 'categoria_produto':
      return 'idcategoria';
    case 'cupom_desconto':
      return 'idcupom';
    case 'dicas_nutricionais':
      return 'iddicas_nutricionais';
    case 'dieta':
      return 'iddieta';
    case 'refeicao':
      return 'idrefeicao';
    case 'dieta_alimentar':
      return 'alimento_id'; // chave composta
    case 'endereco':
      return 'idendereco';
    case 'exercicio':
      return 'idexercicio';
    case 'forum':
      return 'idtopico';
    case 'historico_peso':
      return 'idhistorico_peso';
    case 'historico_treino':
      return 'idhistorico';
    case 'pagamento':
      return 'idpagamento';
    case 'pedido':
      return 'idpedido';
    case 'produto':
      return 'idproduto';
    case 'item_pedido':
      return 'pedido_id'; // chave composta
    case 'meta_usuario':
      return 'idmeta';
    case 'pagamento_detalhe':
      return 'idpagamento2';
    case 'perfil_professor':
      return 'idperfil';
    case 'perfil_usuario':
      return 'idperfil_usuario';
    case 'recuperacao_senha':
      return 'idrecuperacao_senha';
    case 'resposta_forum':
      return 'idresposta';
    case 'treino_exercicio':
      return 'idtreino2';
    default:
      return null;
  }
}

function listarAulaDoDia($idusuario)
{
  $conexao = conectar();

  $sql = "SELECT 
    au.usuario_id,
    ag.idaula,
    ag.data_aula,
    ag.hora_inicio,
    ag.hora_fim,
    tr.idtreino,
    tr.tipo AS tipo_treino,
    tr.descricao AS descricao_treino,
    tr.horario AS horario_treino,
    te.series,
    te.repeticoes,
    te.carga,
    te.intervalo_segundos,
    ex.nome AS nome_exercicio,
    ex.grupo_muscular,
    ex.video_url,
    f.nome AS nome_professor,
    pp.foto_perfil AS foto_professor
  FROM aula_usuario au
  INNER JOIN aula_agendada ag ON au.idaula = ag.idaula
  INNER JOIN treino tr ON ag.treino_id = tr.idtreino
  LEFT JOIN treino_exercicio te ON tr.idtreino = te.treino_id
  LEFT JOIN exercicio ex ON te.exercicio_id = ex.idexercicio
  LEFT JOIN funcionario f ON ag.funcionario_id = f.idfuncionario
  LEFT JOIN perfil_professor pp ON f.usuario_id = pp.usuario_id
  WHERE au.usuario_id = ?
    AND ag.data_aula = CURDATE()";

  $comando = mysqli_prepare($conexao, $sql);
  mysqli_stmt_bind_param($comando, "i", $idusuario);
  mysqli_stmt_execute($comando);
  $resultados = mysqli_stmt_get_result($comando);

  $lista_aulas = [];
  while ($aula = mysqli_fetch_assoc($resultados)) {
    $lista_aulas[] = $aula;
  }

  mysqli_stmt_close($comando);
  desconectar($conexao);

  return $lista_aulas;
}

function listarDados($nomeTabela)
{
    $conexao = conectar();

    if ($nomeTabela !== null) {
        // Por segurança, escapar o nome da tabela
        $nomeTabela = mysqli_real_escape_string($conexao, $nomeTabela);

        $sql = "SELECT * FROM $nomeTabela";
        $comando = mysqli_prepare($conexao, $sql);

        if (!$comando) {
            die('Erro na preparação: ' . mysqli_error($conexao));
        }
    } else {
        return [];
    }

    mysqli_stmt_execute($comando);
    $resultados = mysqli_stmt_get_result($comando);

    $lista = [];
    while ($linha = mysqli_fetch_assoc($resultados)) {
        $lista[] = $linha;
    }

    mysqli_stmt_close($comando);

    return $lista;
}