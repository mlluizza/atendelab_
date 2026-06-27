# AtendeLab

Sistema de Controle de Atendimentos Academicos desenvolvido na disciplina de Fabrica de Software.

## Tecnologias Utilizadas

- PHP 8.x
- MySQL
- phpMyAdmin
- HTML
- CSS
- Bootstrap
- Git e Github

## Funcionalidades Previstas

- Página pública
- Login
- Dashboard
- Cadastro de Pessoas atendidas
- Cadastro de tipos de atendimentos
- Registro de atendimentos
- Relatórios

## Como executar localmente 

1. Acessar pasta htdocs do XAMPP
- Windows: 
```powershell
$ cd C:\xampp\htdocs
```
- Linux:
```bash
cd /opt/lampp/htdocs
```
- MacOS:
```bash
cd /Applications/XAMPP/htdocs
```
2. Clone o repositório para dentro da Pasta
```
git clone <link_repositório>
```
3. Inicie o Apache e o MySQL
4. Crie o banco `atendelab`
5. Importe o script `database/atendelab.sql`
6. Acesse `http://localhost/atendelab/public`