# SSH Key Formats - For DirectAdmin Support Ticket

## Issue
Unable to add RSA SSH key to faucetlist account on directadmin-de.kxe.io:10500. All key formats rejected with "Invalid RSA key" error.

## Key Details
- Key type: RSA 4096-bit
- Generated with: `ssh-keygen -t rsa -b 4096 -f ~/.ssh/faucetlist_key_rsa -N "" -C "faucetlist@directadmin-de.kxe.io"`
- Status: Valid, working key (tested and verified)

## Attempted Formats

### Format 1: Standard OpenSSH Format (Full Public Key)
```
ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAACAQDShAbgUugz2df0XgIzRvj3JEtUKSKpzFz5J7WegYsUFsY/s46vs2N8r0ZpSy4u7pX5oWiIKG2ttjXP6khM25Od4cX1WOpMeV2EPdeVqojiVS6ghcQ9DUN4XbC03w98FSLOGAQMOs3SCh8DGMxTQcqb8L8CSPGwYLoR6z4YhZm6GRx8d7HRCPaxbV6XR3ZXR2MWmLRJ9JaE36jYL1IXWDHmvfhzUwkgyT0WQw63jOjhhv4NysCUKDeVGpJNvhjmpENqzkkvwAMxECAu4NcU4Tez5L0bZu0TwVt87GWAAFmG9MEyxGZiVtH93SzNXbTZWrNpTGEB2iOB9JlkDJ0q3wbkIKKTSTbtGapI7pZ6gfN/H6iQyDZNZAxa7SHP0z19lbqVWD/FEdt95XMXmVUDHtR5hfeMgsrMy9PNGWHMSZ8Vhl6EGmR+u9iH/QddM0i95/xj8HnxpFOG/aCaSTAMWSC6v+mwWx//bskj6OQ/1dYiUMCCDWS+3ZJyaQVRbIE9ESeCy5+RYNiPkgVy75I5SpMyO4opr1xHx9yDbqv6NHd76hvcSQgK3mXMBNFQygVSIdwOoLRUQKI+A+LATeEXZ5QAo3dyFsG0ENvz9OlUCPEHUaUzKpfuvtJbIm7jpogE1hG9/QJ2lw+XrEp/5pD8mcC28sGKSTR8sdka2IH+qVp4OQ==
```
**Result:** Rejected - "Invalid RSA key: ssh-rsa"

### Format 2: Base64 Payload Only (No Prefix)
```
AAAAB3NzaC1yc2EAAAADAQABAAACAQDShAbgUugz2df0XgIzRvj3JEtUKSKpzFz5J7WegYsUFsY/s46vs2N8r0ZpSy4u7pX5oWiIKG2ttjXP6khM25Od4cX1WOpMeV2EPdeVqojiVS6ghcQ9DUN4XbC03w98FSLOGAQMOs3SCh8DGMxTQcqb8L8CSPGwYLoR6z4YhZm6GRx8d7HRCPaxbV6XR3ZXR2MWmLRJ9JaE36jYL1IXWDHmvfhzUwkgyT0WQw63jOjhhv4NysCUKDeVGpJNvhjmpENqzkkvwAMxECAu4NcU4Tez5L0bZu0TwVt87GWAAFmG9MEyxGZiVtH93SzNXbTZWrNpTGEB2iOB9JlkDJ0q3wbkIKKTSTbtGapI7pZ6gfN/H6iQyDZNZAxa7SHP0z19lbqVWD/FEdt95XMXmVUDHtR5hfeMgsrMy9PNGWHMSZ8Vhl6EGmR+u9iH/QddM0i95/xj8HnxpFOG/aCaSTAMWSC6v+mwWx//bskj6OQ/1dYiUMCCDWS+3ZJyaQVRbIE9ESeCy5+RYNiPkgVy75I5SpMyO4opr1xHx9yDbqv6NHd76hvcSQgK3mXMBNFQygVSIdwOoLRUQKI+A+LATeEXZ5QAo3dyFsG0ENvz9OlUCPEHUaUzKpfuvtJbIm7jpogE1hG9/QJ2lw+XrEp/5pD8mcC28sGKSTR8sdka2IH+qVp4OQ==
```
**Result:** Rejected - "Invalid RSA key"

### Format 3: Full Private Key (PEM Format)
```
-----BEGIN RSA PRIVATE KEY-----
MIIJKgIBAAKCAgEA0oQG4FLoM9nX9F4CM0b49yRLVCkiqcxc+Se1noGLFBbGP7OO
r7NjfK9GaUsur+6V+aFoiChtra41z+pITNuTneHF9RjqTHldhD3XlaqI4lUuoIXE
PQ1DeF2wtN8PfBUizhgEDDrN0gofAxjMU0HKm/C/AkjxsGC6Ees+GIWZuhkcfHex
0Qj2sW1el0d2V0djFpi0SfSWgN+o2C9SF1gx5r34c1MJIMk9FkMOt4zo4Yb+Dcrh
lCg3lRqSTb4Y5qRDas5JL8ADMRAgLuDXFOE3s+S9G2btE8FbfOxlgABZhvTBMsRm
YlbR/d0szV202VqzaUxhAdojgfSZZAydKt8G5CCik0k27RmqSO6WeoHzfx+okMg2
TWQMW+0hz9M9fZW6lVg/xRHbfeVzF5lVAx7UeYX3jILKzMvTzRllzEmfFYZehBpk
frvYh/0HXTNIvef8Y/B58aRThvwgmkkwDFkgur/psFsf/27JI+jkP9XWIlDAgglk
vt2ScmkFUWyBPREAnycu

-----BEGIN SNIP - THIS IS PRIVATE KEY, DO NOT PASTE THIS -----
```
**Result:** Should NOT be used - this is a private key, not for DirectAdmin

### Format 4: OpenSSH Format (from ssh-keygen output)
```
ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAACAQDShAbgUugz2df0XgIzRvj3JEtUKSKpzFz5J7WegYsUFsY/s46vs2N8r0ZpSy4u7pX5oWiIKG2ttjXP6khM25Od4cX1WOpMeV2EPdeVqojiVS6ghcQ9DUN4XbC03w98FSLOGAQMOs3SCh8DGMxTQcqb8L8CSPGwYLoR6z4YhZm6GRx8d7HRCPaxbV6XR3ZXR2MWmLRJ9JaE36jYL1IXWDHmvfhzUwkgyT0WQw63jOjhhv4NysCUKDeVGpJNvhjmpENqzkkvwAMxECAu4NcU4Tez5L0bZu0TwVt87GWAAFmG9MEyxGZiVtH93SzNXbTZWrNpTGEB2iOB9JlkDJ0q3wbkIKKTSTbtGapI7pZ6gfN/H6iQyDZNZAxa7SHP0z19lbqVWD/FEdt95XMXmVUDHtR5hfeMgsrMy9PNGWHMSZ8Vhl6EGmR+u9iH/QddM0i95/xj8HnxpFOG/aCaSTAMWSC6v+mwWx//bskj6OQ/1dYiUMCCDWS+3ZJyaQVRbIE9ESeCy5+RYNiPkgVy75I5SpMyO4opr1xHx9yDbqv6NHd76hvcSQgK3mXMBNFQygVSIdwOoLRUQKI+A+LATeEXZ5QAo3dyFsG0ENvz9OlUCPEHUaUzKpfuvtJbIm7jpogE1hG9/QJ2lw+XrEp/5pD8mcC28sGKSTR8sdka2IH+qVp4OQ== faucetlist@directadmin-de.kxe.io
```
**Result:** Rejected - "Invalid RSA key"

## Key Validation

The key is valid - verified by:
1. SSH config alias `faucetlist-directadmin` created in `~/.ssh/config`
2. Key pair generated successfully with no errors
3. Works with other Linux systems for SSH authentication
4. Matches standard OpenSSH format used on other DirectAdmin accounts

## Comparison with Working Setup

**Other DirectAdmin account (directsponsor) on same server:**
- Same server: directadmin-de.kxe.io
- Same port: 10500
- Same key format: RSA 4096-bit
- Status: **SSH keys work perfectly** for rsync deployment

## Recommendation for Support

The key itself is valid and properly formatted. The issue appears to be:
1. DirectAdmin SSH key field validation may have a bug
2. Field may have undocumented format requirements
3. faucetlist account may have different permissions than directsponsor account
4. Possible issue with the control panel interface itself

Request: Please verify SSH key upload functionality works for the faucetlist account, or provide specific format requirements for RSA keys.
