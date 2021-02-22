using System;
using System.Collections.Generic;
using System.Text;

namespace SeleniumTests
{
    class Program
    {

        static void Main(string[] args)
        {

            LoginTests();
            CreateAccountTests();
            Console.WriteLine("Tests Finished");
        }

        static void LoginTests()
        {
            Login.zCorrectCredentials();
            TestEndDelimeter();
            Login.BadUsername();
            TestEndDelimeter();
            Login.BadPassword();
            TestEndDelimeter();
            Login.CorrectUsernameDifferentUsersPassword();
            TestEndDelimeter();
            Login.NoUsername();
            TestEndDelimeter();
            Login.NoPassword();
            TestEndDelimeter();
            Login.NoInputs();
            TestEndDelimeter();
        }

        static void CreateAccountTests()
        {
            NewAccount.PasswordsDontMatch();
            TestEndDelimeter();
            NewAccount.UsernameAlreadyTaken();
            TestEndDelimeter();
        }

        static void TestEndDelimeter()
        {
            Console.WriteLine("\n\n");
        }
    }
}
