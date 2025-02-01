{
  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixpkgs-unstable";
    flake-utils.url = "github:numtide/flake-utils";
  };

  outputs = { self, nixpkgs, flake-utils, ... }:
    flake-utils.lib.eachDefaultSystem (system:
      let
        pkgs = import nixpkgs { inherit system; };
        phpEnv = pkgs.php83.buildEnv {
          extensions = ({ enabled, all }: enabled ++ (with all; [
            xdebug
          ]));
        };
      in
      {
        devShells = {
          default = pkgs.mkShell {
            packages = with pkgs; [
              symfony-cli
              phpEnv
              phpEnv.packages.composer
            ];
          };
        };
      });
}
